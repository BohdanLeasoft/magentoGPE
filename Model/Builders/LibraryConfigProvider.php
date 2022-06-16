<?php

namespace GingerPay\Payment\Model\Builders;

require_once __DIR__.'/../Builders/ConfigRepositoryBuilder.php';

use GingerPay\Payment\Model\Methods\Ideal;
use GingerPay\Payment\Model\Methods\Banktransfer;
use GingerPay\Payment\Model\Methods\KlarnaPayLater;
use GingerPay\Payment\Model\Methods\Afterpay;

class LibraryConfigProvider extends ConfigRepositoryBuilder
{
    /**
     * @var array
     */
    protected $methods = [];

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var PaymentLibraryModel
     */
    protected $paymentLibraryModel;

    /**
     * @var ConfigRepository
     */
    protected $configRepository;
    /**
     * @var PaymentHelper
     */
    protected $paymentHelper;

    /**
     * @param string $code
     *
     * @return MethodInterface|false
     */
    public function getMethodInstance(string $code)
    {
        try {
            return $this->paymentHelper->getMethodInstance($code);
        } catch (\Exception $e) {
            $this->configRepository->addTolog('error', 'Function: getMethodInstance: ' . $e->getMessage());
        }
        return false;
    }

    /**
     * Config Data for checkout
     *
     * @return array
     * @throws \Exception
     */
    public function getConfig(): array
    {
        $config = [];

        if (!$client = $this->paymentLibraryModel->loadGingerClient()) {
            $activeMethods = [];
        } else {
            $activeMethods = $this->getActiveMethods();
        }
        foreach ($this->methodCodes as $code)
        {
            if ($this->methods[$code] && $this->methods[$code]->isAvailable())
            {
                $config['payment'][$code]['instructions'] = $this->getInstructions($code);

                if ($code == Ideal::METHOD_CODE && $client)
                {
                    $config['payment'][$code]['issuers'] = $this->getIssuers($client);
                }
                if ($code == Banktransfer::METHOD_CODE) {
                    $config['payment'][$code]['mailingAddress'] = $this->getMailingAddress($code);
                }
                if ($code == KlarnaPayLater::METHOD_CODE) {
                    $config['payment'][$code]['prefix'] = $this->getCustomerPrefixes();
                }
                if ($code == Afterpay::METHOD_CODE) {
                    $config['payment'][$code]['prefix'] = $this->getCustomerPrefixes();
                    $config['payment'][$code]['conditionsLinkNl'] = Afterpay::TERMS_NL_URL;
                    $config['payment'][$code]['conditionsLinkBe'] = Afterpay::TERMS_BE_URL;
                }

                $config['payment'][$code]['isActive'] = in_array($code, $activeMethods);

                $config['payment'][$code]['logo'] = $this->configRepository->getPaymentLogo($code);
            }
            else
            {
                $config['payment'][$code]['isActive'] = false;
            }
        }

        return $config;
    }

    /**
     * @return array
     */
    public function getActiveMethods()
    {
        return $this->methodCodes;
    }

    /**
     * Instruction data
     *
     * @param string $code
     *
     * @return string
     */
    protected function getInstructions(string $code)
    {
        return nl2br($this->escaper->escapeHtml($this->methods[$code]->getInstructions()));
    }

    /**
     * @param \Ginger\ApiClient $client
     *
     * @return array|bool
     */
    public function getIssuers($client)
    {
        if ($issuers = $this->paymentLibraryModel->getIssuers($client)) {

             return $issuers;
        }
        return false;
    }

    /**
     * @param string $code
     *
     * @return string
     */
    protected function getMailingAddress(string $code): string
    {
        return nl2br($this->escaper->escapeHtml($this->methods[$code]->getMailingAddress()));
    }

    /**
     * @return array
     */
    public function getCustomerPrefixes(): array
    {
        return [
            ['id' => 'male', 'name' => 'Mr.'],
            ['id' => 'female', 'name' => 'Ms.']
        ];
    }

}


