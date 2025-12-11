/**
 * VPay Payment Integration
 * MarriageHub.ng - VPay Dropin Library Integration
 * Date: December 10, 2025
 */

class VPayPayment {
    constructor() {
        this.vpayKey = document.getElementById('vpay-key')?.value || '';
        this.vpayDomain = document.getElementById('vpay-domain')?.value || 'sandbox';
        this.siteUrl = document.getElementById('siteurl')?.value || '';
        this.paymentProvider = document.getElementById('payment-provider')?.value || 'vpay';
    }

    /**
     * Initialize VPay payment
     * @param {Object} options Payment options
     * @returns {Object} VPay dropin instance
     */
    initPayment(options) {
        const {
            amount,
            email,
            transactionRef,
            onSuccess,
            onExit,
            customerName = '',
            customerPhone = '',
            metadata = {}
        } = options;

        // Validate required fields
        if (!this.vpayKey) {
            console.error('VPay public key not configured');
            alert('Payment system not configured. Please contact support.');
            return null;
        }

        if (!amount || !email || !transactionRef) {
            console.error('Missing required payment parameters');
            alert('Invalid payment details. Please try again.');
            return null;
        }

        const vpayOptions = {
            amount: parseFloat(amount),
            currency: 'NGN',
            domain: this.vpayDomain,
            key: this.vpayKey,
            email: email,
            transactionref: transactionRef,
            customer_name: customerName,
            customer_phone: customerPhone,
            customer_logo: this.siteUrl + 'assets/img/logo.png',
            customer_service_channel: '+2348030007000, support@marriagehub.ng',
            metadata: JSON.stringify(metadata),
            onSuccess: function(response) {
                console.log('VPay Payment Success:', response);
                if (typeof onSuccess === 'function') {
                    onSuccess(response);
                }
            },
            onExit: function(response) {
                console.log('VPay Payment Exit:', response);
                if (typeof onExit === 'function') {
                    onExit(response);
                }
            }
        };

        // Wait for VPay library to load
        const checkVPayLoaded = setInterval(() => {
            if (window.VPayDropin) {
                clearInterval(checkVPayLoaded);
                try {
                    const {open, exit} = VPayDropin.create(vpayOptions);
                    return {open, exit};
                } catch (error) {
                    console.error('VPay initialization error:', error);
                    alert('Payment initialization failed. Please try again.');
                    return null;
                }
            }
        }, 100);

        // Timeout after 10 seconds
        setTimeout(() => {
            if (!window.VPayDropin) {
                clearInterval(checkVPayLoaded);
                console.error('VPay library failed to load');
                alert('Payment system not available. Please check your internet connection.');
            }
        }, 10000);
    }

    /**
     * Open VPay payment popup
     * @param {Object} options Payment options
     */
    pay(options) {
        const instance = this.initPayment(options);
        if (instance && instance.open) {
            instance.open();
        }
    }

    /**
     * Generate unique transaction reference
     * @param {String} prefix Reference prefix
     * @returns {String} Unique transaction reference
     */
    static generateReference(prefix = 'VPAY') {
        const timestamp = Date.now();
        const random = Math.floor(Math.random() * 1000000);
        return `${prefix}_${timestamp}_${random}`;
    }

    /**
     * Format amount for VPay (already in kobo/minor units)
     * @param {Number} amount Amount in Naira
     * @returns {Number} Amount in kobo
     */
    static formatAmount(amount) {
        return parseFloat(amount) * 100;
    }
}

// Make VPayPayment globally available
window.VPayPayment = VPayPayment;

// Helper function for quick payments
window.payWithVPay = function(options) {
    const vpay = new VPayPayment();
    vpay.pay(options);
};

console.log('VPay Payment helper loaded successfully');
