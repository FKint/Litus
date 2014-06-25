<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace SyllabusBundle\Component\XMLParser;

use CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Entity\General\AcademicYear as AcademicYearEntity,
    CommonBundle\Entity\User\Person\Academic,
    CommonBundle\Entity\User\Status\University as UniversityStatus,
    DateTime,
    Doctrine\ORM\EntityManager,
    SyllabusBundle\Entity\AcademicYearMap,
    SyllabusBundle\Entity\Study as StudyEntity,
    SyllabusBundle\Entity\Subject as SubjectEntity,
    SyllabusBundle\Entity\SubjectProfMap,
    SyllabusBundle\Entity\StudySubjectMap,
    Zend\Http\Client as HttpClient,
    Zend\Mail\Transport\TransportInterface;

/**
 * Study
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Study
{
    /**
     * @var EntityManager
     */
    private $_entityManager;

    /**
     * @var TransportInterface
     */
    private $_mailTransport;

    /**
     * @var array
     */
    private $_profs;

    /**
     * @var array
     */
    private $_callback;

    /**
     * @var AcademicYearEntity
     */
    private $_academicYear;

    /**
     * @var array
     */
    private $_subjects;

    /**
     * @param EntityManager      $entityManager
     * @param TransportInterface $mailTransport
     * @param string             $xmlPath
     * @param array              $callback
     */
    public function __construct(EntityManager $entityManager, TransportInterface $mailTransport, $xmlPath, $callback)
    {
        $this->_entityManager = $entityManager;
        $this->_mailTransport = $mailTransport;
        $this->_callback = $callback;

        if ($this->_entityManager->getRepository('CommonBundle\Entity\General\Config')->getConfigValue('syllabus.enable_update') != '1')
            return;

        $urls = $this->_getUrls();

        /*
        To add one xml without destroying database:
            add here
                $urls = array(your_xml);
            add return at start of _cleanUpAcademicYear function

            search for duplicate subject-prof mappings and remove them:
                SELECT *
                FROM syllabus.subjects_profs_map AS first
                JOIN syllabus.subjects_profs_map AS second ON first.prof_id = second.prof_id AND first.subject_id = second.subject_id AND first.id != second.id AND first.academic_year = second.academic_year
                ORDER BY first.prof_id
        */

        $this->_callback('progress', 1);

        $counter = 0;

        foreach ($urls as $url) {
            $counter++;
            $entityManager->clear();
            $this->_callback('load_xml', substr($url, strrpos($url, '/') + 1));

            $xml = simplexml_load_file($url);

            $this->_academicYear = $this->_getAcademicYear($xml->properties->academiejaar->startjaar);

            if ($counter <= 1) {
                $this->_cleanUpAcademicYear($this->_academicYear);
                $this->_callback('cleanup', '');
            }

            $this->_subjects = array();
            $this->_profs = array();

            $this->_createSubjects(
                $xml->data->programma->modulegroep,
                $this->_createStudies($xml->data->programma)
            );

            $this->_callback('saving_data', (string) $xml->data->titel);

            $this->getEntityManager()->flush();

            $this->_callback('progress', round($counter/count($urls)*100, 4));
        }
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return $this->_entityManager;
    }

    private function _createStudies($data)
    {
        $this->_callback('create_studies', (string) $data->titel);
        $studies = array();

        $language = trim((string) $data->doceertaal->code);
        $mainTitle = html_entity_decode(ucfirst(trim((string) $data->titel)));

        foreach ($data->fases->children() as $phase) {
            $phaseNumber = (int) $phase->attributes()->code;

            $subStudies = array();
            $studies[$phaseNumber] = array();

            $mainStudy = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\Study')
                ->findOneByKulId($data->attributes()->id);
            if (null == $mainStudy) {
                $mainStudy = new StudyEntity($mainTitle, $data->attributes()->objid, $phaseNumber, $language);
                $this->getEntityManager()->persist($mainStudy);
            } else {
                $mainStudy->setTitle($mainTitle);
            }

            if ($phase->toegestane_combinaties->children()->count() > 0) {
                foreach ($phase->toegestane_combinaties->children() as $studyData) {
                    $title = html_entity_decode(preg_replace('/\([a-zA-Z0-9\s]*\)/', '', $studyData->titel));
                    $titles = explode('+', $title);

                    if (count($titles) == 2) {
                        if (isset($subStudies[(string) $studyData->afstudeerrichting->attributes()->id])) {
                            $subStudy = $subStudies[(string) $studyData->afstudeerrichting->attributes()->id];
                        } else {
                            $subTitle = ucfirst(trim(str_replace(array('Hoofdrichting', 'Nevenrichting', 'Minor', 'Major'), '', $titles[0])));

                            $subStudy = $this->getEntityManager()
                                ->getRepository('SyllabusBundle\Entity\Study')
                                ->findOneByKulId($studyData->afstudeerrichting->attributes()->id);
                            if (null == $subStudy) {
                                $subStudy = new StudyEntity($subTitle, $studyData->afstudeerrichting->attributes()->id, $phaseNumber, $language, $mainStudy);
                                $this->getEntityManager()->persist($subStudy);
                            } else {
                                $subStudy->setTitle($subTitle);
                            }
                            $subStudies[(string) $studyData->afstudeerrichting->attributes()->id] = $subStudy;
                        }

                        $title = $titles[1];
                    } else {
                        $subStudy = $mainStudy;
                    }

                    $subTitle = ucfirst(trim(str_replace(array('Hoofdrichting', 'Nevenrichting', 'Minor', 'Major'), '', $title)));
                    $study = $this->getEntityManager()
                        ->getRepository('SyllabusBundle\Entity\Study')
                        ->findOneByKulId($studyData->attributes()->id);
                    if (null == $study) {
                        $study = new StudyEntity($subTitle, $studyData->attributes()->id, $phaseNumber, $language, $subStudy);
                        $this->getEntityManager()->persist($study);
                    } else {
                        $study->setTitle($subTitle);
                    }

                    $studies[$phaseNumber][(int) $studyData->attributes()->id] = $study;
                    $map = new AcademicYearMap($study, $this->_academicYear);
                    $this->getEntityManager()->persist($map);
                }
            } else {
                $studies[$phaseNumber][0] = $mainStudy;
                $map = new AcademicYearMap($mainStudy, $this->_academicYear);
                $this->getEntityManager()->persist($map);
            }
        }

        return $studies;
    }

    private function _createSubjects($data, $studies)
    {
        $this->_callback('create_subjects');
        if (!isset($data->modulegroep))
            return;

        foreach ($data->modulegroep as $subjects) {
            if ($subjects->tonen_in_programmagids != 'True')
                continue;

            if ($subjects->toegestane_combinaties->children()->count() > 0) {
                $activeStudies = array();
                foreach ($studies as $phaseNumber => $phase) {
                    if (is_array($phase)) {
                        $activeStudies[$phaseNumber] = array();
                        foreach ($phase as $studyId => $study) {
                            foreach ($subjects->toegestane_combinaties->children() as $objId) {
                                if ($studyId == (string) $objId) {
                                    $activeStudies[$phaseNumber][$studyId] = $study;
                                    break;
                                }
                            }
                        }
                    } else {
                        $activeStudies[$phaseNumber] = $phase;
                    }
                }
            } else {
                $activeStudies = $studies;
            }

            if ($subjects->opleidingsonderdelen->children()->count() > 0) {
                foreach ($subjects->opleidingsonderdelen->opleidingsonderdeel as $subjectData) {
                    $code = trim((string) $subjectData->attributes()->code);
                    $this->_callback('create_subjects', (string) $subjectData->titel);

                    $subject = $this->getEntityManager()
                        ->getRepository('SyllabusBundle\Entity\Subject')
                        ->findOneByCode($code);

                    if (null === $subject) {
                        if (isset($this->_subjects[$code])) {
                            $subject = $this->_subjects[$code];
                        } else {
                            $subject = new SubjectEntity($code, html_entity_decode(trim((string) $subjectData->titel)), (int) $subjectData->aanbodperiode, (int) $subjectData->pts);
                            $this->getEntityManager()->persist($subject);
                        }
                    } else {
                        $subject->setName(html_entity_decode(trim((string) $subjectData->titel)));
                    }

                    if (!isset($this->_subjects[$code]))
                        $this->_createProf($subject, $subjectData->docenten->children());

                    $this->_subjects[$code] = $subject;

                    $mandatory = $subjectData->attributes()->verplicht == 'True' ? true : false;

                    foreach ($subjectData->fases->children() as $phase) {
                        $phaseNumber = (int) $phase;

                        if (is_array($activeStudies[$phaseNumber])) {
                            foreach ($activeStudies[$phaseNumber] as $activeStudy) {
                                $map = new StudySubjectMap($activeStudy, $subject, $mandatory, $this->_academicYear);
                                $this->getEntityManager()->persist($map);
                            }
                        } else {
                            $map = new StudySubjectMap($activeStudies[$phaseNumber], $subject, $mandatory, $this->_academicYear);
                            $this->getEntityManager()->persist($map);
                        }
                    }
                }
            }

            if ($subjects->modulegroep->count() > 0)
                $this->_createSubjects($subjects, $activeStudies);
        }
    }

    private function _createProf(SubjectEntity $subject, $profs)
    {
        $maps = array();
        foreach ($profs as $profData) {
            $identification = 'u' . substr(trim($profData->attributes()->persno), 1);

            $prof = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findOneByUniversityIdentification($identification);
            if (null == $prof) {
                if (isset($this->_profs[$identification])) {
                    $prof = $this->_profs[$identification];
                } else {
                    $info = $this->_getInfoProf(trim($profData->attributes()->persno));

                    $prof = new Academic(
                        $identification,
                        array(
                            $this->getEntityManager()
                                ->getRepository('CommonBundle\Entity\Acl\Role')
                                ->findOneByName('prof')
                        ),
                        trim($profData->voornaam),
                        trim($profData->familienaam),
                        $info['email'],
                        $info['phone'],
                        null,
                        $identification
                    );

                    $prof->activate($this->getEntityManager(), $this->_mailTransport, true);

                    $image = $this->_getProfImage($identification, $info['photo']);
                    if ($image)
                        $prof->setPhotoPath($image);

                    $this->getEntityManager()->persist($prof);
                    $this->_profs[$identification] = $prof;
                }
            }

            if ($prof->canHaveUniversityStatus($this->_academicYear)) {
                $prof->addUniversityStatus(
                    new UniversityStatus(
                        $prof,
                        'professor',
                        $this->_academicYear
                    )
                );
            }

            $map = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
                ->findOneBySubjectAndProfAndAcademicYear($subject, $prof, $this->_academicYear);
            if (null == $map) {
                if (isset($maps[$prof->getUniversityIdentification()])) {
                    $map = $maps[$prof->getUniversityIdentification()];
                } else {
                    $map = new SubjectProfMap($subject, $prof, $this->_academicYear);
                    $this->getEntityManager()->persist($map);
                    $maps[$prof->getUniversityIdentification()] = $map;
                }
            }
        }
    }

    private function _cleanUpAcademicYear(AcademicYearEntity $academicYear)
    {
        $mapping = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\AcademicYearMap')
            ->findByAcademicYear($academicYear);

        foreach($mapping as $map)
            $this->getEntityManager()->remove($map);

        $mapping = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\StudySubjectMap')
            ->findByAcademicYear($academicYear);

        foreach($mapping as $map)
            $this->getEntityManager()->remove($map);

        $mapping = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
            ->findByAcademicYear($academicYear);

        foreach($mapping as $map)
            $this->getEntityManager()->remove($map);

        $this->getEntityManager()->flush();
    }

    /**
     * @param string $identification
     */
    private function _getInfoProf($identification)
    {
        $client = new HttpClient();
        $response = $client->setUri('http://www.kuleuven.be/wieiswie/nl/person/' . $identification)
            ->send();

        preg_match('/<noscript>([a-zA-Z0-9\[\]\s\-]*)<\/noscript>/', $response->getBody(), $matches);
        if (count($matches) > 1)
            $email = str_replace(array(' [dot] ', ' [at] '), array('.', '@'), $matches[1]);
        else
            $email = null;

        preg_match('/tel\s\.([0-9\+\s]*)/', $response->getBody(), $matches);
        if (count($matches) > 1)
            $phone = trim(str_replace(' ', '', $matches[1]));
        else
            $phone = null;

        return array(
            'email' => $email,
            'phone' => $phone,
            'photo' => 'http://www.kuleuven.be/wieiswie/nl/person/' . $identification . '/photo',
        );
    }

    private function _getProfImage($identification, $url)
    {
        $headers = get_headers($url);
        if ($headers[0] != 'HTTP/1.1 404 Not Found' && $headers[0] != 'HTTP/1.1 302 Moved Temporarily') {
            file_put_contents('/tmp/' . $identification, file_get_contents($url));
            $finfo = new \finfo;
            $fileinfo = $finfo->file('/tmp/' . $identification, FILEINFO_MIME);
            $mimetype = substr($fileinfo, 0, strpos($fileinfo, ';'));

            if (in_array($mimetype, array('image/jpeg', 'image/jpg', 'image/pjpeg', 'image/png', 'image/gif'))) {
                $filePath = 'public' . $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('common.profile_path');

                $fileName = '';
                do {
                    $fileName = '/' . sha1(uniqid());
                } while (file_exists($filePath . $fileName));

                file_put_contents($filePath . $fileName, file_get_contents('/tmp/' . $identification));
                unlink('/tmp/' . $identification);

                return $fileName;
            }
        }
    }

    /**
     * @param string $type
     */
    private function _callback($type, $extra = null)
    {
        call_user_func($this->_callback, $type, $extra);
    }

    private function _getUrls()
    {
        $url = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('syllabus.root_xml');

        $departments = unserialize(
            $this->_entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('syllabus.department_ids')
            );

        $this->_callback('load_xml', substr($url, strrpos($url, '/') + 1));

        $root = simplexml_load_file($url);

        $studies = array();

        foreach ($root->data->children() as $organization) {
            foreach ($organization->children() as $department) {
                if (in_array($department->attributes()->id, $departments)) {
                    foreach ($department->kwalificatie->children() as $classification) {
                        foreach ($classification->graad as $grade) {
                            foreach ($grade->opleidingen->children() as $study) {
                                $studies[] = array(
                                    'id' => (string) $study->attributes()->id,
                                    'language' => $study->taal,
                                );
                            }
                        }
                    }
                }
            }
        }

        $departmentUrl = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('syllabus.department_url');

        $studyUrl = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('syllabus.study_url');

        $urls = array();

        foreach ($studies as $study) {
            $url = str_replace(
                array(
                    '{{ language }}',
                    '{{ id }}'
                ),
                array(
                    $study['language'],
                    $study['id']
                ),
                $departmentUrl
            );
            $this->_callback('load_xml', substr($url, strrpos($url, '/') + 1));
            $xml = simplexml_load_file($url);

            foreach ($xml->data->opleiding->programmas->children() as $study) {
                $urls[] = str_replace(
                    array(
                        '{{ language }}',
                        '{{ id }}'
                    ),
                    array(
                        strtolower((string) $study->taal->code),
                        (string) $study->attributes()->id,
                    ),
                    $studyUrl
                );
            }
        }

        return $urls;
    }

    private function _getAcademicYear($start)
    {
        $startAcademicYear = AcademicYear::getStartOfAcademicYear(
            new DateTime($start . '-12-25 0:0')
        );
        $academicYear = $this->_entityManager->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByUniversityStart($startAcademicYear);

        if (null === $academicYear) {
            $organizationStart = str_replace(
                '{{ year }}',
                $startAcademicYear->format('Y'),
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('start_organization_year')
            );
            $organizationStart = new DateTime($organizationStart);
            $academicYear = new AcademicYearEntity($organizationStart, $startAcademicYear);
            $this->_entityManager->persist($academicYear);
        }

        return $academicYear;
    }
}
