<?php
/**
 * All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace GingerPay\Payment\Model;

use GingerPay\Payment\Redefiners\Model\ModelBuilderRedefiner;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Escaper;
use Magento\Payment\Helper\Data as PaymentHelper;
use GingerPay\Payment\Redefiners\Model\PaymentLibraryRedefiner as PaymentLibraryModel;
use GingerPay\Payment\Api\Config\RepositoryInterface as ConfigRepository;
use Magento\Payment\Model\MethodInterface;

/**
 * PaymentConfigProvider model class
 */
class PaymentConfigProvider extends ModelBuilderRedefiner
{
    /**
     * @var array
     */
    protected $methodCodes = [
        Methods\Bancontact::METHOD_CODE,
        Methods\Banktransfer::METHOD_CODE,
        Methods\Creditcard::METHOD_CODE,
        Methods\ApplePay::METHOD_CODE,
        Methods\Ideal::METHOD_CODE,
        Methods\KlarnaPayNow::METHOD_CODE,
        Methods\KlarnaPayLater::METHOD_CODE,
        Methods\Paypal::METHOD_CODE,
        Methods\Payconiq::METHOD_CODE,
        Methods\Afterpay::METHOD_CODE,
        Methods\Amex::METHOD_CODE,
        Methods\Tikkie::METHOD_CODE,
        Methods\Googlepay::METHOD_CODE,
        Methods\Sofort::METHOD_CODE,
        Methods\KlarnaDirectDebit::METHOD_CODE,
    ];

    /**
     * PaymentConfigProvider constructor.
     *
     * @param PaymentLibrary              $paymentLibraryModel
     * @param ConfigRepository $configRepository
     * @param PaymentHelper    $paymentHelper
     * @param Escaper          $escaper
     */
    public function __construct(
        PaymentLibraryModel         $paymentLibraryModel,
        ConfigRepository $configRepository,
        PaymentHelper    $paymentHelper,
        Escaper          $escaper
    ) {
        $this->paymentLibraryModel = $paymentLibraryModel;
        $this->configRepository = $configRepository;
        $this->escaper = $escaper;
        $this->paymentHelper = $paymentHelper;
        foreach ($this->methodCodes as $code) {
            $this->methods[$code] = $this->getMethodInstance($code);
        }
    }


}
