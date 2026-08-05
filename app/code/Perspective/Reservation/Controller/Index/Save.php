<?php
declare(strict_types=1);

namespace Perspective\Reservation\Controller\Index;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Perspective\Reservation\Api\ReservationManagementInterface;

/**
 * Saving of a reservation.
 */
class Save implements HttpPostActionInterface
{
    /**
     * @var RequestInterface
     */
    protected RequestInterface $request;

    /**
     * @var JsonFactory
     */
    protected JsonFactory $jsonFactory;

    /**
     * @var ReservationManagementInterface
     */
    protected ReservationManagementInterface $reservationManagement;

    /**
     * Save constructor.
     *
     * @param RequestInterface $request
     * @param JsonFactory $jsonFactory
     * @param ReservationManagementInterface $reservationManagement
     */
    public function __construct(
        RequestInterface $request,
        JsonFactory $jsonFactory,
        ReservationManagementInterface $reservationManagement,
    ) {
        $this->request = $request;
        $this->jsonFactory = $jsonFactory;
        $this->reservationManagement = $reservationManagement;
    }

    /**
     * @return Json
     */
    public function execute()
    {

//        $this->cancelReservations->execute();
//
//        (die);
        $resultJson = $this->jsonFactory->create();
        $params = $this->request->getParams();

        if (empty($params['name']) || empty($params['email']) || empty($params['product_id'])) {
            return $resultJson->setData([
                'success' => false,
                'message' => __('Please fill out all required fields.')
            ]);
        }

        try {
            $order = $this->reservationManagement->createReservationOrder($params);

            return $resultJson->setData([
                'success' => true,
                'message' => __('Thank you! The item has been successfully reserved for 24 hours. Order number: ')
                    . $order->getIncrementId()
            ]);
        } catch (LocalizedException $e) {
            return $resultJson->setData([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            return $resultJson->setData([
                'success' => false,
                'message' => __('An error occurred while processing the reservation.')
            ]);
        }
    }
}
