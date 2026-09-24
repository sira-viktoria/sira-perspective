<?php
declare(strict_types=1);

namespace Perspective\Voting\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Perspective\Voting\Service\ConfigData;

/**
 * VotingActions UI Component.
 */
class VotingActions extends Column
{
    private const string URL_EDIT_PATH = 'voting/index/edit';
    private const string URL_DELETE_PATH = 'voting/index/delete';

    /**
     * @var UrlInterface
     */
    protected UrlInterface $urlBuilder;

    /**
     * @var ConfigData
     */
    protected ConfigData $configDataService;

    /**
     * VotingActions constructor.
     *
     * @param UrlInterface $urlBuilder
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param ConfigData $configDataService
     * @param array $components
     * @param array $data
     */
    public function __construct(
        UrlInterface $urlBuilder,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ConfigData $configDataService,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->configDataService = $configDataService;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['voting_id']) && $this->configDataService->isAdminAllowedEditVotes()) {
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_EDIT_PATH,
                                [
                                    'voting_id' => $item['voting_id'
                                    ],
                                ]
                            ),
                            'label' => __('Edit'),
                        ],


                        'delete' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_DELETE_PATH,
                                [
                                    'voting_id' => $item['voting_id'
                                    ],
                                ]
                            ),
                            'label' => __('Delete'),
                        ],
                    ];
                }
            }
        }
        return $dataSource;
    }
}
