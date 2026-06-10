<?php
/**
 * LiqPay Extension for Magento 2.
 *
 * @author PerspectiveTeam<order@perspectiveteam.com>
 * © Perspective. All rights reserved
 */
declare(strict_types=1);

namespace Perspective\LiqPay\Plugin\Framework\App\Request;

use \Magento\Framework\App\Request\CsrfValidator as CsrfValidatorOriginal;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
/**
 * CsrfValidator Plugin.
 */
class CsrfValidator
{

    /**
     * @var UrlInterface
     */
    protected $urlInterface;

    public function __construct(
        UrlInterface $urlInterface
    ) {
        $this->urlInterface = $urlInterface;
    }

    /**
     * @param CsrfValidatorOriginal $subject
     * @param \Closure $proceed
     * @param RequestInterface $request
     * @param ActionInterface $action
     * @return void
     */
    public function aroundValidate(
        CsrfValidatorOriginal $subject,
        \Closure $proceed,
        RequestInterface $request,
        ActionInterface $action
    ): void
    {
        return;
//        if (str_contains($request->getPathInfo(), 'liqpay') !== false) {
//            return;
//        }

//        $proceed($request, $action);
    }
}
