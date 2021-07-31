const stripe = Stripe(stripePublicKey);
        const elements = stripe.elements();
        const style = {
            base: {
                color: "#32325d",
                fontFamily: 'Arial, sans-serif',
                fontSmoothing: "antialiased",
                fontSize: "16px",
                "::placeholder": {
                color: "#32325d"
                }
            },
            invalid: {
                fontFamily: 'Arial, sans-serif',
                color: "#fa755a",
                iconColor: "#fa755a"
            }
        };

        const card = elements.create("card", { style: style });
        // Stripe injects an iframe into the DOM
        card.mount("#card-element");
        card.on("change", function (event) {
            // Disable the Pay button if there are no card details in the Element
            document.querySelector("button").disabled = event.empty;
            document.querySelector("#card-error").textContent = event.error ? event.error.message : "";
        });

        const form = document.getElementById("payment-form");
        form.addEventListener("submit", function(event) {
            event.preventDefault();

            $.ajax({url: suspicionUrl });

            console.log(clientSecret);
            // Complete payment when the submit button is clicked
            stripe
                .confirmCardPayment(clientSecret, {
                    payment_method: {
                        card: card
                    }
                })
                .then(function(result) {
                    if (result.error) {
                        // Show error to your customer
                        showError(result.error.message);
                        //$.ajax({url: unsuspectUrl });
                    } else {
                        // The payment succeeded!
                        //orderComplete(result.paymentIntent.id);*
                        console.log(result);
                        window.location.href = redirectAfterSuccessUrl;
                    }
                });
        });

        var orderComplete = function(paymentIntentId) {
            loading(false);
            document
                .querySelector(".result-message a")
                .setAttribute(
                "href",
                "https://dashboard.stripe.com/test/payments/" + paymentIntentId
                );
            document.querySelector(".result-message").classList.remove("hidden");
            document.querySelector("button").disabled = true;
            };

        // Show the customer the error from Stripe if their card fails to charge
        const showError = function(errorMsgText) {
            loading(false);
            const errorMsg = document.querySelector("#card-error");
            errorMsg.textContent = errorMsgText;
            setTimeout(function() {
                errorMsg.textContent = "";
            }, 4000);
        };

        // Show a spinner on payment submission
        const loading = function(isLoading) {
            if (isLoading) {
                // Disable the button and show a spinner
                document.querySelector("button").disabled = true;
                document.querySelector("#spinner").classList.remove("hidden");
                document.querySelector("#button-text").classList.add("hidden");
            } else {
                document.querySelector("button").disabled = false;
                document.querySelector("#spinner").classList.add("hidden");
                document.querySelector("#button-text").classList.remove("hidden");
            }
        };