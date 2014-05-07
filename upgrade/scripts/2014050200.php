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

pg_query($connection, 'ALTER TABLE nodes.forms ADD send_guest_login_mail BOOLEAN');
pg_query($connection, 'UPDATE nodes.forms SET send_guest_login_mail = FALSE');

$config = getConfigValue($connection, 'form.mail_confirmation');

$config = unserialize($config);

foreach($config as $key => $item) {
    $config[$key]['content'] = str_replace('%entry_summary%', '%entry_summary%' . PHP_EOL . PHP_EOL . '%guest_login%', $item['content']);
}

updateConfigKey($connection, 'form.mail_confirmation', serialize($config));
