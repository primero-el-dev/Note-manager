<?php

namespace App\Traits;

use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Contracts\Service\Attribute\Required;

trait DocumentManagerTrait
{
	protected DocumentManager $dm;

	#[Required]
	public function setDocumentManager(DocumentManager $dm): void
	{
		$this->dm = $dm;
	}
}