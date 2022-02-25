<?php

namespace GingerPay\Payment\Model\Builders;


use GingerPay\Payment\Service\Transaction\Process\Error;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Prophecy\Exception\Exception;
use Magento\Store\Model\App\Emulation;
use Magento\Framework\App\Area;
use Magento\Store\Model\Store;

class HelperDataBuilder extends AbstractHelper
{
    public $quoteManagement;
    public $cartManagement;
    public $store;
    /**
     * @var Emulation
     */
    protected $emulation;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\Data\Form\FormKey $formkey,
        \Magento\Quote\Model\QuoteFactory $quote,
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Sales\Model\Service\OrderService $orderService,
        CartManagementInterface $cartManagement,
        Emulation $emulation,
        Store $store
    ) {
        $this->storeManager = $storeManager;
        $this->product = $product;
        $this->formkey = $formkey;
        $this->quote = $quote;
        $this->quoteManagement = $quoteManagement;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->productRepository = $productRepository;
        $this->orderService = $orderService;
        $this->cartManagement = $cartManagement;
        $this->emulation = $emulation;
        $this->store = $store;
        parent::__construct($context);

    }

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createOrder($order)
    {
        $store = $this->storeManager->getStore();
        $storeId = $store->getId();
        $this->emulation->startEnvironmentEmulation($storeId, Area::AREA_ADMINHTML);


        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        $customer = $this->customerFactory->create();
        $customer->setWebsiteId($websiteId);
        $customer->loadByEmail($order['email']); // load customet by email address
        if (!$customer->getEntityId()) {
            //If not avilable then create this customer
            $customer->setWebsiteId($websiteId)->setStore($this->store)->setFirstname($order['shipping_address']['firstname'])->setLastname($order['shipping_address']['lastname'])->setEmail($order['email'])->setPassword($order['email']);
            $customer->save();
        }

        $this->writeToFile('a1','test');///////////////////////

        $quote = $this->quote->create(); // Create Quote Object
        $quote->setStore($this->store); // Set Store
        $customer = $this->customerRepository->getById($customer->getEntityId());
        $quote->setCurrency();
        $quote->assignCustomer($customer); // Assign quote to Customer
        //add items in quote

        foreach ($order['items'] as $item) {
            $product = $this->productRepository->getById($item['product_id']);
            $product->setPrice($item['price']);

            $quote->addProduct($product, intval($item['qty']));
        }

        $this->writeToFile('a2','test');///////////////////////

        $quote->getBillingAddress()->addData($order['shipping_address']);
        $quote->getShippingAddress()->addData($order['shipping_address']);
        // Collect Rates and Set Shipping & Payment Method
        $shippingAddress = $quote->getShippingAddress();
        $shippingAddress->setCollectShippingRates(true)->collectShippingRates()->setShippingMethod($order['shipping_method']);
        $quote->setPaymentMethod('checkmo');
        $quote->setInventoryProcessed(false);
        $quote->save();

        $this->writeToFile('a3','test');///////////////////////

        // Set Sales Order Payment
        $quote->getPayment()->importData(['method' => 'checkmo']);
        // Collect Totals & Save Quote

        $this->writeToFile('a4','test');///////////////////////

        $quote->collectTotals()->save();

        $this->writeToFile('a5','test');///////////////////////
        // Create Order From Quote
        $orderdata = $this->quoteManagement->placeOrder($quote->getId());
        $orderdata = $this->quoteManagement->submit($quote);

        $this->writeToFile('a','test');

        $orderdata->setEmailSent(0);

        $this->writeToFile('a6','test');///////////////////////
        $increment_id = $orderdata->getRealOrderId();
        return $increment_id;
        $this->writeToFile('a7','test');///////////////////////

        if ($orderdata->getEntityId()) {
            $this->writeToFile('a8','test');///////////////////////
            $result['order_id'] = $orderdata->getRealOrderId();
        } else {
            $this->writeToFile('a9','test');///////////////////////
            $result = ['error' => 1, 'msg' => 'Your custom message'];
        }
        $this->writeToFile('a10','test');///////////////////////
       // return $result;
    }

    public function writeToFile($fileName, $text)
    {
        $file = fopen(__DIR__."/".$fileName.".json", "w+");
        fwrite( $file, $text);

        fclose($file);
    }

}


//
//use Magento\Sales\Api\Data\OrderInterface;
//use Magento\Framework\App\Helper\AbstractHelper;
//use Magento\Sales\Api\Data\OrderAddressInterface;
//use Magento\Framework\DataObject;
//use Magento\Sales\Api\Data\OrderItemInterface;
////use Magento\Framework\App\Helper\Context;
////use Magento\Store\Model\StoreManagerInterface;
////use Magento\Catalog\Model\Product;
////use Magento\Framework\Data\Form\FormKey;
////use Magento\Quote\Model\Quote;
////use Magento\Customer\Model\CustomerFactory;
////use Magento\Sales\Model\Service\OrderService;
////use Magento\Quote\Model\QuoteFactory;
//
//class HelperDataBuilder extends AbstractHelper
//{
//    public function __construct(
//        \Magento\Framework\App\Helper\Context $context,
//        \Magento\Store\Model\StoreManagerInterface $storeManager,
//        \Magento\Customer\Model\CustomerFactory $customerFactory,
//        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
//        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
//        \Magento\Quote\Model\QuoteFactory $quote,
//        \Magento\Quote\Model\QuoteManagement $quoteManagement,
//        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender
//
//    ) {
//        $this->storeManager = $storeManager;
//        $this->customerFactory = $customerFactory;
//        $this->productRepository = $productRepository;
//        $this->customerRepository = $customerRepository;
//        $this->quote = $quote;
//        $this->quoteManagement = $quoteManagement;
//        $this->orderSender = $orderSender;
//        parent::__construct($context);
//    }
//    /*
//    * create order programmatically
//    */
//    public function createOrder(OrderInterface $orderInfo)
//    {
//        $store = $this->storeManager->getStore();
//        $storeId = $store->getStoreId();
//        $websiteId = $this->storeManager->getStore()->getWebsiteId();
//        $customer = $this->customerFactory->create();
//        $customer->setWebsiteId($websiteId);
//        $customer->loadByEmail($orderInfo->getCustomerEmail());// load customer by email address
//
//        if (!$customer->getId()) {
//            //For guest customer create new customer
//            $customer->setWebsiteId($websiteId)
//                ->setStore($store)
//                ->setFirstname($orderInfo->getCustomerFirstname())
//                ->setLastname($orderInfo->getCustomerLastname())
//                ->setEmail($orderInfo->getCustomerEmail())
//                ->setPassword($orderInfo->getCustomerEmail());
//            $customer->save();
//        }
//
//        $quote = $this->quote->create(); //Create object of quote
//        $quote->setStore($store); //set store for our quote
//        /* for registered customer */
//        $customer = $this->customerRepository->getById($customer->getId());
//        $quote->setCurrency();
//        $quote->assignCustomer($customer); //Assign quote to customer
//        $items = $orderInfo->getItems();
//
//        //add items in quote
//        foreach ($items as $item) {
//
//            $product = $this->productRepository->getById($item->getProductId());
//
//            $quote->addProduct($product, intval($item->getQtyOrdered()));
////            if (!empty($item['super_attribute'])) {
////                /* for configurable product */
////                $buyRequest = $item;
////                $quote->addProduct($product, $buyRequest);
////            } else {
////                /* for simple product */
////                $quote->addProduct($product, intval($item->getQtyOrdered()));
////            }
//        }
//
//        $billingAddress = $this->getAddressArray($orderInfo->getBillingAddress());
//
//        //Set Billing and shipping Address to quote
//        $quote->getBillingAddress()->addData($billingAddress);
//        $quote->getShippingAddress()->addData($billingAddress);
//
//
//        // set shipping method
//        $shippingAddress = $quote->getShippingAddress();
//        $shippingAddress->setCollectShippingRates(true)
//            ->collectShippingRates()
//            ->setShippingMethod('flatrate_flatrate'); //shipping method, please verify flat rate shipping must be enable
//        $quote->setPaymentMethod($orderInfo->getPayment()->getMethod()); //payment method, please verify checkmo must be enable from admin
//        $quote->setInventoryProcessed(false); //decrease item stock equal to qty
//        $quote->save(); //quote save
//        // Set Sales Order Payment, We have taken check/money order
//
//        $quote->getPayment()->importData(['method' => $orderInfo->getPayment()->getMethod()]);
//
//        // Collect Quote Totals & Save
//        $quote->collectTotals()->save();
//
//        // Create Order From Quote Object
//        $order = $this->quoteManagement->submit($quote);
//
//        /* for send order email to customer email id */
//
//        $this->orderSender->send($order);
//        /* get order real id from order */
//        $orderId = $order->getIncrementId();
//
//      //  return $orderInfo->getCustomerEmail();
//
//
//        if ($orderId) {
//            $result['success'] = $orderId;
//        } else {
//            $result = ['error' => true, 'msg' => 'Error occurs for Order placed'];
//        }
//        return $result;
//    }
//
//    private function getAddressArray(OrderAddressInterface $orderAddress)
//    {
//        $addressArray = array_filter([
//            'firstname'    => $orderAddress->getFirstname(),
//            'lastname'     => $orderAddress->getLastname(),
//            'prefix' => $orderAddress->getPrefix(),
//            'suffix' => $orderAddress->getSuffix(),
//            'street' => $orderAddress->getStreet(),
//            'city' => $orderAddress->getCity(),
//            'country_id' => $orderAddress->getCountryId(),
//            'region' => $orderAddress->getRegion(),
//            'region_id' => $orderAddress->getRegionId(), // State region id
//            'postcode' => $orderAddress->getPostcode(),
//            'telephone' => $orderAddress->getTelephone(),
//            'fax' => $orderAddress->getFax(),
//            'save_in_address_book' => 1
//        ]);
//        return $addressArray;
//    }
//
//
//
////     /**
////     * @var Context
////     */
////    protected $context;
////    /**
////     * @var StoreManagerInterface
////     */
////    protected $storeManager;
////    /**
////     * @var Product
////     */
////    protected $product;
////    /**
////     * @var FormKey
////     */
////    protected $formKey;
////    /**
////     * @var Quote
////     */
////    protected $quote;
////    /**
////     * @var CustomerFactory
////     */
////    protected $customerFactory;
////
////    /**
////     * @param Context $context
////     * @param StoreManagerInterface $storeManager
////     * @param Product $product
////     * @param FormKey $formKey
////     * @param Quote $quote,
////     * @param CustomerFactory $customerFactory,
////     * @param OrderService $orderService,
////     */
////    public function __construct(
////        Context $context,
////        StoreManagerInterface $storeManager,
////        Product $product,
////        FormKey $formkey,
////        QuoteFactory $quote,
////        QuoteManagement $quoteManagement,
////        CustomerFactory $customerFactory,
////        CustomerRepositoryInterface $customerRepository,
////        OrderService $orderService
////    ) {
////        $this->_storeManager = $storeManager;
////        $this->_product = $product;
////        $this->_formkey = $formkey;
////        $this->quote = $quote;
////        $this->quoteManagement = $quoteManagement;
////        $this->customerFactory = $customerFactory;
////        $this->customerRepository = $customerRepository;
////        $this->orderService = $orderService;
////        parent::__construct($context);
////    }
////
////    /**
////     * Create Order On Your Store
////     *
////     * @param array $orderData
////     * @return array
////     *
////     */
////    public function createMageOrder($orderData) {
////        $store=$this->_storeManager->getStore();
////        $websiteId = $this->_storeManager->getStore()->getWebsiteId();
////        $customer=$this->customerFactory->create();
////        $customer->setWebsiteId($websiteId);
////        $customer->loadByEmail($orderData['email']);// load customet by email address
////
////        if(!$customer->getEntityId()){
////            //If not avilable then create this customer
////            $customer->setWebsiteId($websiteId)
////                ->setStore($store)
////                ->setFirstname($orderData['shipping_address']['firstname'])
////                ->setLastname($orderData['shipping_address']['lastname'])
////                ->setEmail($orderData['email'])
////                ->setPassword($orderData['email']);
////            $customer->save();
////        }
////        $quote=$this->quote->create(); //Create object of quote
////        $quote->setStore($store); //set store for which you create quote
////        // if you have allready buyer id then you can load customer directly
////        $customer= $this->customerRepository->getById($customer->getEntityId());
////        $quote->setCurrency();
////        $quote->assignCustomer($customer); //Assign quote to customer
////
////        //add items in quote
////        foreach($orderData['items'] as $item){
////            $product=$this->_product->load($item['product_id']);
////            $product->setPrice($item['price']);
////            $quote->addProduct(
////                $product,
////                intval($item['qty'])
////            );
////        }
////
////        //Set Address to quote
////        $quote->getBillingAddress()->addData($orderData['shipping_address']);
////        $quote->getShippingAddress()->addData($orderData['shipping_address']);
////
////        // Collect Rates and Set Shipping & Payment Method
////
////        $shippingAddress=$quote->getShippingAddress();
////        $shippingAddress->setCollectShippingRates(true)
////            ->collectShippingRates()
////            ->setShippingMethod('freeshipping_freeshipping'); //shipping method
////        $quote->setPaymentMethod('checkmo'); //payment method
////        $quote->setInventoryProcessed(false); //not effetc inventory
////        $quote->save(); //Now Save quote and your quote is ready
////
////        // Set Sales Order Payment
////        $quote->getPayment()->importData(['method' => 'checkmo']);
////
////        // Collect Totals & Save Quote
////        $quote->collectTotals()->save();
////
////        // Create Order From Quote
////        $order = $this->quoteManagement->submit($quote);
////
////        $order->setEmailSent(0);
////        $increment_id = $order->getRealOrderId();
////        if($order->getEntityId()){
////            $result['order_id']= $order->getRealOrderId();
////        }else{
////            $result=['error'=>1,'msg'=>'Your custom message'];
////        }
////        return $result;
////    }
//}
