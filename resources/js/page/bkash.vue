<template>
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-xl-6 col-lg-8 col-sm-10 light-bg px-3 py-5 text-center">
				<h2>Bkash Payment with Laravel And Vuejs</h2>
				<img src="/img/bkash.jpg" width="300" />
				<br />
				<button
					class="mt-2 btn btn-info"
					id="bKash_button"
					:disabled="disable == 1"
				>
					Pay With Bkash
				</button>
			</div>
		</div>
	</div>
</template>

<script>
const axios = require("axios").default;
export default {
	data() {
		return {
			disable: 1,
			invoiceId: Date.now(),
			amount: 10,
		};
	},
	mounted() {
		this.getBkashToken();
	},

	methods: {
		/**
		 * Get Token
		 */
		getBkashToken() {
			let self = this;
			axios
				.post("/api/bkash-payment-token-create", {
					order_code: this.invoiceId,
				})
				.then(function (response) {
					if (response.data.success) {
						self.initBkash(response.data.result);
					}
				})
				.catch(function (error) {});
		},
		//Init bKash payment
		initBkash(resdata) {
			//Pay Amount
			let amount = this.amount;
			//Authorization Token
			let token = resdata.authToken.id_token;
			let invoiceNumber = this.invoiceId;
			let paymentID = null;
			//Init bKash
			bKash.init({
				paymentMode: "checkout",
				paymentRequest: {
					amount: amount,
					intent: resdata.intent,
					currency: resdata.currency,
					merchantInvoiceNumber: resdata.merchantInvoiceNumber,
				},
				//Payment create
				createRequest() {
					axios
						.post("/api/bkash-create-payment-request", {
							token: token,
							amount: amount,
							merchantInvoiceNumber: invoiceNumber,
						})
						.then(function (response) {
							var data = response.data.data;
							if (data && data.paymentID != null) {
								paymentID = data.paymentID;
								//Call iFrame
								data.errorCode = null;
								data.errorMessage = null;
								bKash.create().onSuccess(data);
							} else {
								bKash.create().onError();
								toastr.error("Payment Failed");
							}
						})
						.catch(function (error) {
							bKash.create().onError();
						});
				},
				//Payment Execute
				executeRequestOnAuthorization() {
					axios
						.post("/api/bkash-execute-payment-request", {
							paymentID: paymentID,
							token: token,
						})
						.then(function (response) {
							let result = response.data.data;
							if (result && result.paymentID != null) {
								$("#bKashFrameWrapper").fadeOut();
								$("#hiddenButton").click();
								toastr.success("Payment Success");
							} else {
								bKash.execute().onError();
								toastr.error("Payement Failed");
							}
						})
						.catch(function (error) {
							bKash.create().onError();
						});
				},
				//On Success
				onSuccess() {
					alert("payment success");
				},
				//Close
				onClose() {
					toastr.error("Payment Cancelled");
				},
				onError() {
					toastr.error("Payment Failed");
				},
			});
			this.disable = 0;
			$("#bKash_button").removeAttr("disabled");
		},
	},
};
</script>

