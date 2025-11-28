import { createFileRoute, Link } from '@tanstack/react-router'
import { useState } from 'react'
// import { OptimizedImage } from '../../components/Image/Image'

// ✅ STATIC DEMO DATA - No API calls, no search params needed
// const demoCoupon = {
//   id: 3,
//   coupon_code: "AIR123456",
//   operator: {
//     name: "Airtel",
//     logo_url: "/m2.png"
//   },
//   plan_type: { name: "Prepaid Unlimited" },
//   selling_price: { formatted: "$25.99" },
//   denomination: { formatted: "$35.00" },
//   validity_days: 30,
//   images: [{ url: "/m2.png" }]
// }
//
// const demoPlan = {
//   id: "standard",
//   name: "Standard",
//   price: 299,
//   data: "2GB",
//   speed: "50Mbps"
// }

const CheckoutPage: React.FC = () => {
  const [step, setStep] = useState<'details' | 'payment-methods' | 'processing' | 'success'>('details')
  const [selectedPaymentMethod, setSelectedPaymentMethod] = useState<'paypal' | 'wallet' | 'stripe' | null>(null)
  const [isProcessing, setIsProcessing] = useState(false)

  // const planPrice = demoPlan.price
  // const planName = demoPlan.name
  // const planData = demoPlan.data
  // const planSpeed = demoPlan.speed
  // const coupon = demoCoupon
  //
  const handlePaymentMethodSelect = (method: 'paypal' | 'wallet' | 'stripe') => {
    setSelectedPaymentMethod(method)
    setStep('payment-methods')
  }

  const handleProceedToPayment = () => {
    if (!selectedPaymentMethod) return

    setIsProcessing(true)
    setStep('processing')

    setTimeout(() => {
      setIsProcessing(false)
      setStep('success')
    }, 2500)
  }

  const handleBackToDetails = () => {
    setSelectedPaymentMethod(null)
    setStep('details')
  }

  // ✅ SUCCESS PAGE
  if (step === 'success') {
    return (
      <div className="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-teal-50 py-12">
        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center space-y-8">
            <div className="mx-auto w-28 h-28 bg-emerald-100 rounded-full flex items-center justify-center">
              <svg className="w-16 h-16 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
              </svg>
            </div>
            <div className="max-w-3xl mx-auto space-y-6">
              <h1 className="text-4xl sm:text-5xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                Payment Successful!
              </h1>
              <p className="text-xl text-gray-600 leading-relaxed">
                Your mobile recharge has been processed successfully. The coupon code will be
                delivered to your account within minutes.
              </p>
              <div className="bg-gradient-to-r from-emerald-50/80 to-teal-50/80 rounded-2xl p-8 border border-emerald-200/30 backdrop-blur-sm">
                <div className="grid md:grid-cols-2 gap-8">
                  <div>
                    <h3 className="font-semibold text-emerald-800 mb-4">Order Details</h3>
                    <div className="space-y-3 text-sm">
                      <div className="flex justify-between">
                        <span>Operator:</span>
                        <span className="font-medium">Airtel</span>
                      </div>
                      <div className="flex justify-between">
                        <span>Plan:</span>
                        <span className="font-medium">Standard</span>
                      </div>
                      <div className="flex justify-between">
                        <span>Code:</span>
                        <span className="font-mono bg-emerald-100 px-3 py-1 rounded-full">AIR123456</span>
                      </div>
                      <div className="flex justify-between pt-3 border-t">
                        <span className="font-semibold">Total Paid:</span>
                        <span className="text-2xl font-bold text-emerald-700">$299</span>
                      </div>
                    </div>
                  </div>
                  <div className="text-center">
                    <div className="w-24 h-24 mx-auto rounded-xl overflow-hidden bg-gradient-to-br from-emerald-500/10 to-teal-500/10 border-2 border-emerald-200/50">
                      <img
                        src="https://via.placeholder.com/96x96/0066cc/ffffff?text=COUPON"
                        alt="Coupon"
                        className="w-full h-full object-cover"
                      />
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div className="flex flex-col sm:flex-row gap-4 justify-center max-w-2xl mx-auto">
              <Link to="/" className="btn btn-primary btn-lg flex-1 h-14 text-lg">
                Continue Shopping
              </Link>
              <Link to="/profile" className="btn btn-outline btn-lg flex-1 h-14 text-lg">
                View Orders
              </Link>
            </div>
          </div>
        </div>
      </div>
    )
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-50 via-white to-blue-50 py-6 lg:py-12">
      <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        {/* Header */}
        <div className="flex items-center justify-between mb-8">
          <Link to="/product/3" className="btn btn-ghost btn-lg flex items-center gap-3">
            <svg width="24" height="24" viewBox="0 0 1024 1024" className="w-5 h-5">
              <path fill="currentColor" d="M224 480h640a32 32 0 1 1 0 64H224a32 32 0 0 1 0-64z"/>
              <path fill="currentColor" d="m237.248 512 265.408 265.344a32 32 0 0 1-45.312 45.312l-288-288a32 32 0 0 1 0-45.312l288-288a32 32 0 1 1 45.312 45.312L237.248 512z"/>
            </svg>
            Back to Product
          </Link>
          <div className="text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
            Secure Checkout
          </div>
        </div>

        {/* Steps Indicator */}
        <div className="flex justify-center mb-8">
          <div className="flex items-center gap-6">
            <div className={`flex items-center gap-2 ${step !== 'details' ? 'text-primary' : 'text-gray-500'}`}>
              <div className={`w-8 h-8 rounded-full flex items-center justify-center font-semibold ${
                step !== 'details'
                  ? 'bg-primary text-white'
                  : 'bg-gray-200 text-gray-600'
              }`}>
                1
              </div>
              <span className="hidden sm:block font-medium">Order Details</span>
            </div>
            <div className="w-12 h-1 bg-gradient-to-r from-primary/30 to-secondary/30"></div>
            <div className={`flex items-center gap-2 ${step === 'payment-methods' || step === 'processing' }`}>
              <div className={`w-8 h-8 rounded-full flex items-center justify-center font-semibold ${
                step === 'payment-methods' || step === 'processing'
              }`}>
                2
              </div>
              <span className="hidden sm:block font-medium">Payment</span>
            </div>
          </div>
        </div>

        <div className="grid lg:grid-cols-3 gap-8 lg:gap-12">
          {/* Order Details */}
          <div className="lg:col-span-2 space-y-6">
            <div className="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 p-6 lg:p-8">
              <h2 className="text-2xl font-bold text-gray-900 mb-6">Order Summary</h2>

              <div className="flex items-center gap-6 p-6 bg-gradient-to-r from-primary/5 to-secondary/5 rounded-2xl border border-primary/20">
                <div className="relative w-24 h-24 rounded-xl overflow-hidden shadow-lg">
                  <img
                    src="/m2.png"
                    alt="Airtel Standard"
                    className="w-full h-full object-cover"
                    loading="eager"
                  />

                </div>

                <div className="flex-1 min-w-0">
                  <h3 className="text-xl font-bold text-gray-900 truncate">
                    Airtel
                  </h3>
                  <p className="text-sm text-gray-600 mt-1 truncate">Standard</p>
                  <p className="text-xs text-gray-500 mt-2">
                    Code: <span className="font-mono bg-gray-100 px-2 py-1 rounded-md">
                      AIR123456
                    </span>
                  </p>
                  <p className="text-sm text-gray-600 mt-2 flex flex-wrap gap-4">
                    <span className="font-mono">2GB</span>
                    <span>•</span>
                    <span>50Mbps</span>
                  </p>
                </div>

                <div className="text-right">
                  <div className="text-3xl font-bold text-primary">
                    $299
                  </div>
                  <div className="mt-2 text-sm text-gray-500 line-through">
                    $35.00
                  </div>
                  <div className="mt-1 text-xs bg-emerald-100 text-emerald-800 px-2 py-1 rounded-full inline-block">
                    You save $10.01
                  </div>
                </div>
              </div>

              {step === 'details' && (
                <div className="alert mt-2 alert-info bg-gradient-to-r from-blue-50 to-indigo-50 border-blue-200">
                  <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                  </svg>
                  <span>Review your order details above and choose your preferred payment method</span>
                </div>
              )}
            </div>
          </div>

          {/* Payment Methods & Summary */}
          <div className="lg:col-span-1 space-y-6">
            <div className="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 p-6 lg:p-8 sticky top-6">
              <h2 className="text-xl font-bold text-gray-900 mb-6">
                {step === 'details' ? 'Choose Payment Method' : 'Payment Summary'}
              </h2>

              {step === 'details' && (
                <div className="space-y-4">
                  {/* PayPal */}
                  <button
                    onClick={() => handlePaymentMethodSelect('paypal')}
                    className="w-full flex items-center justify-between p-4 border-2 rounded-xl transition-all duration-300 hover:shadow-lg hover:shadow-primary/10 border-gray-200 hover:border-primary/40 group"
                  >
                    <div className="flex items-center gap-4">

                      <div>
                        <h3 className="font-semibold text-gray-900">PayPal</h3>
                        <p className="text-sm text-gray-600">Safe and secure online payments</p>
                      </div>
                    </div>
                    <div className="flex items-center gap-2">
                      <span className="text-lg font-bold text-primary">$299</span>
                      <svg className="w-5 h-5 text-primary group-hover:rotate-180 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                      </svg>
                    </div>
                  </button>

                  {/* Wallet */}
                  <button
                    onClick={() => handlePaymentMethodSelect('wallet')}
                    className="w-full flex items-center justify-between p-4 border-2 rounded-xl transition-all duration-300 hover:shadow-lg hover:shadow-emerald/10 border-gray-200 hover:border-emerald-400 group"
                  >
                    <div className="flex items-center gap-4">

                      <div>
                        <h3 className="font-semibold text-gray-900">Wallet Balance</h3>
                        <p className="text-sm text-gray-600">$5,000 available</p>
                      </div>
                    </div>
                    <div className="flex items-center gap-2">
                      <span className="text-lg font-bold text-emerald-600">$299</span>
                      <svg className="w-5 h-5 text-emerald-600 group-hover:rotate-180 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                      </svg>
                    </div>
                  </button>

                  {/* Stripe */}
                  <button
                    onClick={() => handlePaymentMethodSelect('stripe')}
                    className="w-full flex items-center justify-between p-4 border-2 rounded-xl transition-all duration-300 hover:shadow-lg hover:shadow-purple/10 border-gray-200 hover:border-purple-400 group"
                  >
                    <div className="flex items-center gap-4">

                      <div>
                        <h3 className="font-semibold text-gray-900">Card (Stripe)</h3>
                        <p className="text-sm text-gray-600">Visa, MasterCard, Amex</p>
                      </div>
                    </div>
                    <div className="flex items-center gap-2">
                      <span className="text-lg font-bold text-purple-600">$299</span>
                      <svg className="w-5 h-5 text-purple-600 group-hover:rotate-180 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                      </svg>
                    </div>
                  </button>
                </div>
              )}

              {step === 'payment-methods' && selectedPaymentMethod && (
                <div className="space-y-4">
                  <div className="flex items-center justify-between p-4 bg-primary/10 rounded-xl border border-primary/20">
                    <span className="font-semibold text-primary">
                      Selected: {selectedPaymentMethod === 'paypal' ? 'PayPal' :
                                selectedPaymentMethod === 'wallet' ? 'Wallet Balance' : 'Credit/Debit Card'}
                    </span>
                    <button
                      onClick={handleBackToDetails}
                      className="btn btn-ghost btn-sm"
                    >
                      Change
                    </button>
                  </div>

                  <button
                    onClick={handleProceedToPayment}
                    disabled={isProcessing}
                    className="btn btn-success w-full btn-lg h-14 text-lg font-bold"
                  >
                    {isProcessing ? (
                      <>
                        <span className="loading loading-spinner loading-sm"></span>
                        Processing Payment...
                      </>
                    ) : (
                      <>
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Pay $299 Now
                      </>
                    )}
                  </button>
                </div>
              )}

              {step === 'processing' && (
                <div className="space-y-6 text-center">
                  <div className="mx-auto w-20 h-20 bg-gradient-to-br from-primary/20 to-secondary/20 rounded-full flex items-center justify-center">
                    <span className="loading loading-spinner loading-lg text-primary"></span>
                  </div>
                  <div className="space-y-3">
                    <h3 className="text-xl font-semibold text-gray-900">
                      Processing your payment...
                    </h3>
                    <p className="text-gray-600">
                      Please wait while we process your payment securely.
                    </p>
                  </div>
                  <div className="flex justify-center">
                    <div className="flex gap-4">
                      <div className="w-2 h-2 bg-primary/30 rounded-full animate-bounce"></div>
                      <div className="w-2 h-2 bg-primary/50 rounded-full animate-bounce" style={{animationDelay: '0.1s'}}></div>
                      <div className="w-2 h-2 bg-primary rounded-full animate-bounce" style={{animationDelay: '0.2s'}}></div>
                    </div>
                  </div>
                </div>
              )}

              {/* Order Summary - Always Visible */}
              <div className="pt-6 border-t border-gray-200/50">
                <h3 className="font-semibold text-gray-900 mb-4">Order Summary</h3>
                <div className="space-y-3">
                  <div className="flex justify-between">
                    <span className="text-sm">Subtotal:</span>
                    <span className="font-semibold">$299</span>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span>Discount:</span>
                    <span className="text-emerald-600">Applied</span>
                  </div>
                  <div className="flex justify-between">
                    <span className="text-sm">Tax (0%):</span>
                    <span className="font-semibold">$0</span>
                  </div>
                  <hr className="border-gray-200 my-3" />
                  <div className="flex justify-between items-center">
                    <span className="text-lg font-bold">Total:</span>
                    <span className="text-2xl font-bold text-primary">$299</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}

export const Route = createFileRoute('/checkout/')({
  component: CheckoutPage,
})
