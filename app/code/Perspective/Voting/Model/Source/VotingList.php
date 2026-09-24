<?php
declare(strict_types=1);

namespace Perspective\Voting\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Perspective\Voting\Model\ResourceModel\Voting\CollectionFactory;

/**
 * VotingList Class.
 */
class VotingList implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $collectionFactory;

    /**
     * VotingList constructor.
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $collection = $this->collectionFactory->create();
        $options = [];

        foreach ($collection as $voting) {
            $options[] = [
                'value' => $voting->getId(),
                'label' => $voting->getTitle()
            ];
        }
        return $options;
    }
}
