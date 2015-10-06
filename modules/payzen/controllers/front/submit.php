<?php
/**
 * PayZen payment module 1.2f (revision 61545)
 *
 * Compatible with V2 payment platform. Developped for Prestashop 1.5.0.x.
 * Support contact: support@payzen.eu.
 * 
 * Copyright (C) 2014 Lyra Network (http://www.lyra-network.com/) and contributors
 * 
 * 
 * NOTICE OF LICENSE
 *
 * This source file is licensed under the Open Software License version 3.0
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
*/

/**
 * This controller manages return from PayZen payment gateway.
 */
class PayzenSubmitModuleFrontController extends ModuleFrontController {
	public $display_column_left = false;
	
	private $currentCart;
	
	public function postProcess() {
		$cartId = Tools::getValue('vads_order_id');
		$this->currentCart = new Cart((int)$cartId);
		
		$this->module->logger->logInfo("User return to shop process starts for cart #$cartId.");
		
		// Cart errors
		if (!Validate::isLoadedObject($this->currentCart) || $this->currentCart->nbProducts() <= 0) {
			$this->module->logger->logWarning("Cart is empty, redirect to home page. Cart ID: $cartId.");
			Tools::redirectLink('index.php');
		}
		
		if ($this->currentCart->id_customer == 0 || $this->currentCart->id_address_delivery == 0 || $this->currentCart->id_address_invoice == 0 || !$this->module->active) {
			$this->module->logger->logWarning("No address selected for customer or module disabled, redirect to checkout first page. Cart ID: $cartId.");
			Tools::redirect('index.php?controller=order&step=1');
		}
		
		$customer = new Customer($this->currentCart->id_customer);
		if (!Validate::isLoadedObject($customer)) {
			$this->module->logger->logWarning("Customer not logged in, redirect to checkout first page. Cart ID: $cartId.");
			Tools::redirect('index.php?controller=order&step=1');
		}
		
		$this->_processPaymentReturn();
	}
	
	private function _processPaymentReturn() {
		/** @var PayzenResponse $payzenResponse */
		$payzenResponse = new PayzenResponse(
				$_REQUEST,
				Configuration::get('PAYZEN_MODE'),
				Configuration::get('PAYZEN_KEY_TEST'),
				Configuration::get('PAYZEN_KEY_PROD')
		);
		
		$cartId = $this->currentCart->id;
		
		// Check the authenticity of the request
		if (!$payzenResponse->isAuthentified()) {
			$this->module->logger->logError("Cart #$cartId : authentication error ! Redirect to home page.");
			Tools::redirectLink('index.php');
		}
		
		// Search order in db
		$orderId = Order::getOrderByCartId($cartId);
		
		if ($orderId == false) { 
			// order has not been processed yet
			
			if ($payzenResponse->isAcceptedPayment()) {
				$this->module->logger->logWarning("Payment for cart #$cartId has been processed by client return ! This means the check URL did not work.");
				
				$newState = $this->module->isOneyPendingPayment($payzenResponse) /* Oney payments */ ?
							Configuration::get('PAYZEN_OS_ONEY_PENDING') :
							Configuration::get('PS_OS_PAYMENT');
				
				$this->module->logger->logInfo("Payment accepted for cart #$cartId. New order status is $newState.");
				$order = $this->module->saveOrder($this->currentCart, $newState, $payzenResponse);
					
				// redirect to success page
				$this->_redirectSuccess($order, $this->module->id, $payzenResponse, true);
			} else {
				// payment KO

				if(Configuration::get('PAYZEN_FAILURE_MANAGEMENT') == Payzen::ON_FAILURE_SAVE || $this->module->isOney($payzenResponse)) {
					// save on failure option is selected or oney payment : save order and go to history page
					$newState = $payzenResponse->isCancelledPayment() ? Configuration::get('PS_OS_CANCELED') : Configuration::get('PS_OS_ERROR');
					
					$this->module->logger->logWarning("Payment for order #$cartId has been processed by client return ! This means the check URL did not work.");
					
					$msg = $this->module->isOney($payzenResponse) ? 'FacilyPay Oney payment' : 'Save on failure option is selected';
					$this->module->logger->logInfo("$msg : save failed order for cart #$cartId. New order status is $newState.");
					
					$this->module->saveOrder($this->currentCart, $newState, $payzenResponse);
					
					$this->module->logger->logInfo("Redirect to history page, cart ID : #$cartId.");
					Tools::redirect('index.php?controller=history');
				} else {
					// option 2 choosen : get back to checkout process and show message
					$this->module->logger->logInfo("Payment failed, redirect to order checkout page, cart ID : #$cartId.");
					
					$controller = Configuration::get('PS_ORDER_PROCESS_TYPE') ? 'order-opc' : 'order' . (Tools::version_compare(_PS_VERSION_, '1.5.1', '>=') ? '&step=3' : '');
					Tools::redirect('index.php?controller=' . $controller . '&payzen_pay_error=yes');
				}
			}
		} else {
			
			// order already registered
			$this->module->logger->logInfo("Order already registered for cart #$cartId.");
			
			$order = new Order((int)$orderId);
			$oldState = $order->getCurrentState();
			
			switch ($oldState) {
				case Configuration::get('PS_OS_ERROR'):
				case Configuration::get('PS_OS_CANCELED'):
					
					$this->module->logger->logInfo("Save on failure option is selected or FacilyPay Oney payment. Order for cart #$cartId is in a failed status.");
					
					if($payzenResponse->isAcceptedPayment()) {
						// order saved with failed status while payment is successful
						$this->module->logger->logError("Payment success received from platform while order is in a failed status for cart #$cartId.");
					} else {
						// just display a failure confirmation message
						$this->module->logger->logInfo("Payment failure confirmed for cart #$cartId.");
					}
					
					$this->module->logger->logInfo("Redirect to history page. Cart ID : #$cartId.");
					Tools::redirect('index.php?controller=history');
					
					break;
			
				case Configuration::get('PAYZEN_OS_ONEY_PENDING'):
				case (($oldState == Configuration::get('PS_OS_OUTOFSTOCK')) && $this->module->isOney($payzenResponse)):
			
					$this->module->logger->logInfo("Order for cart #$cartId is saved but waiting FacilyPay Oney confirmation. Update order status according to payment result.");
						
					if ($payzenResponse->isPendingPayment()) {
						// redirect to success page
						$this->module->logger->logInfo("FacilyPay Oney pending status confirmed for cart #$cartId. Just redirect to success page.");
						$this->_redirectSuccess($order, $this->module->id, $payzenResponse);
					} else {
						// order is pending Oney confirmation, payment is not pending : error case
						$this->module->logger->logError("Order saved with FacilyPay Oney pending status while payment not pending, cart ID : #$cartId.");
						Tools::redirect('index.php?controller=order-confirmation&id_cart=' . $cartId
								. '&id_module=' . $this->module->id . '&id_order=' . $order->id
								. '&key=' . $order->secure_key . '&error=yes');
					}
					
					break;
						
				case Configuration::get('PS_OS_PAYMENT'):
				case Configuration::get('PAYZEN_OS_PAYMENT_OUTOFSTOCK'):
				case (($oldState == Configuration::get('PS_OS_OUTOFSTOCK')) && !$this->module->isOney($payzenResponse)):
				default:
						
					if($payzenResponse->isAcceptedPayment()) {
						// redirect to success page
						$this->module->logger->logInfo("Payment success confirmed for cart #$cartId. Just redirect to success page.");
						$this->_redirectSuccess($order, $this->module->id, $payzenResponse);
					} else {
						// order saved with success status while payment failed
						$this->module->logger->logError("Order saved with success status while payment failed, cart ID : #$cartId.");
						Tools::redirect('index.php?controller=order-confirmation&id_cart=' . $cartId
								. '&id_module=' . $this->module->id . '&id_order=' . $order->id
								. '&key=' . $order->secure_key . '&error=yes');
					}
			
					break;
			
				default:
					
					// order saved with unmanaged status, redirect client according to payment result
					$this->module->logger->logInfo("Order saved with unmanaged status for cart #$cartId, redirect client according to payment result.");
					
					if($payzenResponse->isAcceptedPayment()) {
						// redirect to success page
						$this->module->logger->logInfo("Payment success for cart #$cartId. Redirect to success page.");
						$this->_redirectSuccess($order, $this->module->id, $payzenResponse);
					} else {
						$this->module->logger->logInfo("Payment failure for cart #$cartId. Redirect to history page.");
						Tools::redirect('index.php?controller=history');
					}
					
					break;
			}
		}
	}
	
	private function _redirectSuccess($order, $id_module, $payzenResponse, $check=false) {
		// Just display a confirmation message
		$link = 'index.php?controller=order-confirmation&id_cart=' . $order->id_cart
				. '&id_module=' . $id_module . '&id_order=' . $order->id
				. '&key=' . $order->secure_key;
			
		// Amount paid not equals initial amount. Error !
		if (number_format($order->total_paid, 2) != number_format($payzenResponse->getFloatAmount(), 2)) {
			$link .= "&error=yes";
		}
			
		if(Configuration::get('PAYZEN_MODE') == 'TEST') {
			if($check) {
				// ctx_mode=TEST => the user is the webmaster
				// order has not been paid, but we receive a successful payment code => automatic response didn't work
				// So we display a warning about the not working check_url
				
				$link .= "&check_url_warn=yes";
			}
			
			
			$link .= "&prod_info=yes";
		}
			
		Tools::redirect($link);
	}
}