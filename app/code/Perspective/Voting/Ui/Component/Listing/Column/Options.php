<?php
declare(strict_types=1);

namespace Perspective\Voting\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Perspective\Voting\Model\ResourceModel\VotingOption\CollectionFactory as OptionCollectionFactory;

/**
 * Options UI Component.
 */
class Options extends Column
{
    /**
     * @var OptionCollectionFactory
     */
    protected OptionCollectionFactory $optionCollectionFactory;

    /**
     * Options Constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param OptionCollectionFactory $optionCollectionFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface        $context,
        UiComponentFactory      $uiComponentFactory,
        OptionCollectionFactory $optionCollectionFactory,
        array                   $components = [],
        array                   $data = []
    )
    {
        $this->optionCollectionFactory = $optionCollectionFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $votingIds = array_column($dataSource['data']['items'], 'voting_id');

            $optionCollection = $this->optionCollectionFactory->create();
            $optionCollection->addFieldToFilter('voting_id', ['in' => $votingIds]);

            $optionsByVoting = [];
            foreach ($optionCollection as $option) {
                $votingId = $option->getVotingId();
                $optionsByVoting[$votingId][] = $option;
            }

            foreach ($dataSource['data']['items'] as & $item) {
                $votingId = (int)$item['voting_id'];

                if (isset($optionsByVoting[$votingId])) {
                    $formattedOptions = [];
                    foreach ($optionsByVoting[$votingId] as $option) {
                        $label = sprintf('%s (%d)', $option->getTitle(), $option->getTotalVotes());
                        $formattedOptions[] = $label;
                    }

                    $item[$this->getData('name')] = implode('<br>', $formattedOptions);
                }
            }
        }
        return $dataSource;
    }
}
