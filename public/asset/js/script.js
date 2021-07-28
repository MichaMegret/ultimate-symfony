$(document).ready(function () {
    $("body").on("click", "a.js-addToCart", function(e){
        e.preventDefault();
        const url = $(this).attr("href");
        const parent=$(this).parent();
        const ligne = $(this).parents(".cartLine");
        $.ajax({
            type: "POST",
            url: url,
            dataType: "json",
            success: function (response) {

                if(response.showMessage==1 || response.code=="error"){
                    var message = response.message;
                    var type = response.type;
                    $("#messageMasterContainer").append("<div class='alert alert-"+type+"'>"+message+"</div>");
                }


                if(response.code=="success"){
                    var amountTotalCart = response.cartAmount / 100;
                    var itemsTotalCart = response.cartItems;
                    $(".amountTotalCart").text(amountTotalCart);
                    $("#itemsTotalCart").text(itemsTotalCart);

                    if(parent.find(".qtyItem").length){
                        totalItem = response.totalAmountItem / 100;
                        parent.find(".qtyItem").text(response.nbThisProduct);
                        ligne.find(".js-amountTotalItem").text(totalItem+" €");
                    }

                }

                setTimeout(() => {
                    $("#messageMasterContainer").html("");
                }, 5000);
            }
        });
    })


    $("body").on("click", "a.js-removeToCart", function(e){
        e.preventDefault();
        const url = $(this).attr("href");
        const parent=$(this).parent();
        const ligne = $(this).parents(".cartLine");
        $.ajax({
            type: "POST",
            url: url,
            dataType: "json",
            success: function (response) {

                if(response.showMessage==1 || response.code=="error"){
                    var message = response.message;
                    var type = response.type;
                    $("#messageMasterContainer").append("<div class='alert alert-"+type+"'>"+message+"</div>");
                }

                if(response.code=="success"){
                    var amountTotalCart = response.cartAmount / 100;
                    var itemsTotalCart = response.cartItems;
                    $(".amountTotalCart").text(amountTotalCart);
                    $("#itemsTotalCart").text(itemsTotalCart);

                    if(parent.find(".qtyItem").length){
                        if(response.nbThisProduct>0){
                            totalItem = response.totalAmountItem / 100;
                            parent.find(".qtyItem").text(response.nbThisProduct);
                            ligne.find(".js-amountTotalItem").text(totalItem+" €");
                        }
                        else{
                            parent.parents(".table").replaceWith('<h3 class="text-center">Aucun article dans votre panier</h3>');
                        }
                    }

                }

                setTimeout(() => {
                    $("#messageMasterContainer").html("");
                }, 5000);
            }
        });
    })
});