<?php

namespace Admin\Form\Article;

use \Litus\Form\Admin\Decorator\ButtonDecorator;

use Zend\Form\Element\Submit;

class Edit extends \Admin\Form\Article\Add
{

    public function __construct($options = null)
    {
        parent::__construct($options);
		
        $this->removeElement('submit');

		$submit = new Submit('submit');
        $submit->setLabel('Edit')
                ->setAttrib('class', 'textbooks_edit')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($submit);
    }

	public function populate($article)
	{
		$data = array(
			'title' => $article->getTitle(),
			'author' => $article->getMetaInfo()->getAuthors(),
			'publisher' => $article->getMetaInfo()->getPublishers(),
			'year_published' => $article->getMetaInfo()->getYearPublished(),
			'purchase_price' => number_format($article->getPurchasePrice()/100, 2),
			'sellprice_nomember' => number_format($article->getSellPrice()/100, 2),
			'sellprice_member' => number_format($article->getSellPriceMembers()/100, 2),
			'barcode' => $article->getBarcode(),
			'supplier' => $article->getSupplier()->getId(),
			'bookable' => $article->isBookable(),
			'unbookable' => $article->isUnbookable(),
			'can_expire' => $article->canExpire(),
			'internal' => $article->isInternal()
		);
		
		if ($article->isInternal()) {
			$data['nb_black_and_white'] = $article->getNbBlackAndWhite();
			$data['nb_colored'] = $article->getNbColored();
			$data['binding'] = $article->getBinding()->getId();
			$data['official'] = $article->isOfficial();
			$data['rectoverso'] = $article->isRectoVerso();
			$data['front_color'] = $article->getFrontColor()->getId();
		}
		
		parent::populate($data);
	}

}