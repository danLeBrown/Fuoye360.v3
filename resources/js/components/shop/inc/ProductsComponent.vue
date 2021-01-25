<template>
    <div class="display-products">
        <div v-for="product in products" v-bind:key="product.id+'-'+product.name" class="product-div">
            <span class='product-category'>{{product.category}}</span>
            <div class="img-col">
                <span v-if="product.status == 1" class="product-status text-error">
                    Pre-Order
                </span>
                <span v-else-if="product.status == 2" class="product-status text-error">
                    Available
                </span>
                <span v-else-if="product.status == 3" class="product-status text-error">
                    Sold-Out
                </span>
                <span class="product-price alert-success">₦{{product.price}}</span>
                <img loading="lazy" class="product-image" :src="asset('storage/product_images/'+product.image)" :alt="product.name"><br>
            </div>
            <div for="" class="min-descrip">
                {{product.name.length > 10 ? product.name.substr(0,10)+'...' : product.name}}
                <br>
            </div>
            <div class="btn-col">
                <button type="button" name="button" class="modal-btn btn-success" @click="showModal(product.id, product)" style="font-size:.6rem"><i class="fas fa-ellipsis-h"></i></button>
            </div>
            <table style="display:none;">
                <tr>
                    <td :class="'tablecategory_'+product.id">{{product.category}}</td>
                    <td v-if="product.status == 1" :class="'tablestatus_'+product.id">
                        Pre-Order
                    </td>
                    <td v-else-if="product.status == 2" :class="'tablestatus_'+product.id">
                        Available
                    </td>
                    <td v-else-if="product.status == 3" :class="'tablestatus_'+product.id">
                        Sold-Out
                    </td>
                    <td :class="'tablename_'+product.id">{{product.name}}</td>
                    <td :class="'tableimg_'+product.id">{{asset('storage/product_images/'+product.image)}}</td>
                    <td :class="'tableprice_'+product.id">{{product.price}}</td>
                    <td :class="'tabledescription_'+product.id" v-html="product.description"></td>
                    <td :class="'tablesellerimg_'+product.id">{{asset('storage/profile_images/'+product.user.image)}}</td>
                    <td :class="'tableshopname_'+product.id">{{product.user.name}}</td>
                    <td :class="'tablesellerid_'+product.id">{{product.user_id}}</td>
                    <td :class="'tableproductid_'+product.id">{{product.id}}</td>
                </tr>
            </table> 
        </div>

        <div id="product-modal" class="modal vs-wrapper">
            <div class="modal-content" style="border-radius:1rem;padding:.5rem;">
                <div class="modal-header">
                    <span class='modal-product-category'></span>
                    <span class="seller-id" style="display:none;"></span>
                    <span id="product-id" style="display:none;"></span>
                    <h3 class="modal-product-name sb-text"></h3>
                    <span class="close-modal closeBtn">&times;</span>
                </div>
                <div style="background: var(--bg-color);border-radius:1rem;padding: 0 .5rem .5rem;">
                    <div class="modal-product-content">
                        <div class="modal-image-col">
                            <div class="modal-image" id="render-block">
                                <span class="modal-product-status">
                                </span>
                                <span class="modal-product-price"></span>
                                <img loading="lazy" src="" alt="product-image" style="
                                min-width: 130px;" @click="viewImage"/>
                            </div>
                            
                        <div class="shop-info" v-if="$route.params.page != 'inventory' && $route.name   != 'visit'">
                                <p class="sb-text" style="margin: 0 auto; margin-bottom: .5em; width:75%;border-radius:1rem;border:none;"><i class="fas fa-info-circle"></i>
                                Shop info
                                </p>
                                <a href="#" @click.prevent="visitUser" class="shop-info-profile">
                                    <img loading="lazy" src="" class="seller-image"/>
                                    <span class="seller-name" style="font-weight: bold;margin-left:.5rem;word-break:break-word;"></span>
                                </a>
                            </div>
                        </div>
                        <div class="modal-label">
                            <div>
                                <div style="padding: 0;" class="success">Details</div>
                                <span class="modal-product-details"></span>
                            </div>
                            <div>
                                <form class="" action="index.html" method="post">
                                    <div class="modal-form-qty">
                                        <label class="success" for="order-qty">Quantity</label>
                                        <div class="form-qty-func">
                                            <button type="button" @click="minusQty">&minus;</button>
                                            <input type="number" class="modal-product-qty" name="modal-product-qty" id="order-qty" :disabled="$route.params.page == 'cart'" @change="checkQty" v-model="form.qty">
                                            <button type="button" @click="addQty">&plus;</button>
                                        </div>
                                    </div>
                                    <div style="margin-top : .5rem; position: relative;">
                                        <label class="success"><i class="fas fa-share-alt"></i></label>
                                        <span class="social-links" style="color: #111;">
                                        </span>
                                    </div>
                                    <div class="modal-product-view-col"><i class="fa fa-eye"></i> 
                                        <span id="modal-product-view"></span>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="modal-footer-container" v-if="$route.params.page == 'inventory'">
                                <div></div>
                                <div class="modal-footer-btn">
                                    <button type="button" name="button" class="closeBtn ">Close</button>
                                    <button type="submit" name="button" class="modal-btn-delete" id="delete-product" @click="confirmDelete"><i class="fas fa-trash"></i> Delete</button>
                                    <button type='button' class='modal-btn-success modal-edit' onclick="editProduct()"><i class='fas fa-edit'></i> Edit</button>
                                    <button type="submit" class="modal-btn-success modal-update" style="display:none;" onclick="updateProduct()"><i class="fas fa-check"></i> Update</button>
                                </div>
                            </div>
                            <div v-else class="modal-footer-container">
                                <div class="wishlist-btn-col" v-if="showWishlist">
                                    <button v-if="!addWishlist" type="button" name="button" class="rmv-wishlist" @click="removeFromWishlist(form.productid)"><i class="far fa-bookmark"></i></button>
                                    <button v-else type="button" name="button" @click="addToWishlist(form.productid)" class="wishlist"><i class="far fa-bookmark"></i></button>
                                </div>
                                <div v-else>

                                </div>
                                <div class="modal-footer-btn" v-if="$route.params.page != 'inventory'">
                                    <button type="button" name="button" class="closeBtn ">Close</button>
                                    <button v-if="showWishlist" type='submit' class='modal-btn-success add-to-cart' @click="addToCart($event)"><i class='fas fa-shopping-cart'></i> Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ["products", "user"],
    data(){
        return{
            form: {
                productid: '',
                action: '',
                sellername: '',
                sellerid: '',
                sellerimgsource: '',
                page: '',
                qty: 1,
                id: ''
            },
            showWishlist: '',
            addWishlist: '',
            product: {}
        }
    },
    mounted(){
        $(".closeBtn").click(function () {
            closeModal();
        });
        $('#product-modal').on('click', function(e) {
            if ($(e.target).closest(".modal-content").length === 0) {
                closeModal();
            }
        });
        
        function closeModal(){
            $("#product-modal").hide();
        }
    },
    methods:{
        viewImage(){
            this.$emit('viewImage', $(".tableimg_" + this.form.id).text());
        },
        showModal(id, product){
            this.product = product;
            this.form.qty = 1;
            this.form.id = id;
            $(".seller-id").text($(".tablesellerid_" + id).text());
            this.form.productid = $(".tableproductid_" + id).text();
            this.form.sellerid = $(".tablesellerid_" + id).text();
            this.form.sellername = $(".tableshopname_" + id).text();
            if (this.user.id != this.form.sellerid) {
                this.showWishlist = true;
            }else{
                this.showWishlist = false;
            }
            axios.post('/api/action/'+id+'/update-views')
            .then((res)=>{
                if (res.data.data.status == 200) {
                    $("#modal-product-view").html(res.data.data.views);
                    this.products.forEach(product => {
                        if(product.id == id && product.in_wishlist == true){
                            this.addWishlist = false;
                        }else if(product.id == id && product.in_wishlist == false){
                            this.addWishlist = true;
                        }
                    });
                }else{
                    $(".modal-form-qty, .wishlist, .add-to-cart").hide();
                    $("#modal-product-view").html(res.data.data.views);
                }
            })
            .catch((err)=>console.log(err));
            $(".modal-product-category").text($(".tablecategory_" + id).text());
            $(".modal-product-status").text($(".tablestatus_" + id).text());
            $(".modal-product-name").text($(".tablename_" + id).text());
            $(".modal-product-price").text("₦" + $(".tableprice_" + id).text());
            $('.modal-image img').attr("src", $(".tableimg_" + id).text());
            $('.modal-product-details').html($(".tabledescription_" + id).html());
            this.form.sellerimgsource = $(".tablesellerimg_" + id).text();
            $(".seller-image").attr("src", this.form.sellerimgsource);

            // $(".shop-info-profile").attr("href", "/" + this.form.sellername + "?v=shop&pid=" + id);
            $(".seller-name").text(this.form.sellername);
            $("#product-id").text($(".tableproductid_" + id).text());
            $(".social-links").html("<a href='https://api.whatsapp.com/send?phone=&text=Guys! I just found an awesome product on ```FUOYE360 SHOP```. Check it out on https://fuoye360.com/shop/buy?pid=" + id + "' target='_blank'><i class='fab fa-whatsapp'></i></a> <a href='https://twitter.com/intent/tweet?text=Guys! I just found an awesome product on FUOYE360 SHOP. Check it out on https://fuoye360.com/shop/buy?pid=" + id + " %23fuoye360%23fuoye360shop' target='_blank'><i class='fab fa-twitter'></i></a>");
            setTimeout(() => {
                $("#product-modal").show();
            }, 800); 
        },
        confirmDelete(){
            this.$emit('confirmDelete', this.product);
        },
        visitUser(){
            this.$router.push({name: 'visit', params:{username: this.form.sellername}, query:{v: this.$route.name, pid: this.form.productid}});
        },
        addQty(){            
            return ++this.form.qty;
        },
        minusQty(){
            if (this.form.qty <= 1) {
              return  this.form.qty = 1;   
            }
            return --this.form.qty;
        },
        checkQty(){
            if(this.form.qty < 1){
                this.form.qty = 1;
            }
        },
        closeModal(){
            $("#product-modal").hide();
            this.form.qty = 1;        
        },
        addToWishlist(id){
            axios.post('/api/action/'+id+'/add-to-wishlist')
            .then((res)=>{
                if (res.data.data.status == 200) {
                    this.products.forEach(product => {
                        if(product.id == id){
                            product.in_wishlist = true;
                            this.addWishlist = false;
                        } 
                    });
                }
            })
            .catch((err)=>console.log(err));
        },
        removeFromWishlist(id){
            axios.post('/api/action/'+id+'/remove-from-wishlist')
            .then((res)=>{
                if (res.data.data.status == 200) {
                    this.products.forEach(product => {
                        if(product.id == id){
                            product.in_wishlist = false;
                            this.addWishlist = true;
                        } 
                        if (this.$route.params.page == 'wishlist') {
                            axios.get('/api/shop/wishlist').then((res)=>{
                                this.$emit('updateWishlist', { products : res.data.data.products});
                            }).catch((err)=>{
                                this.errors = err.response.data.errors;
                                this.$emit('alertNotification', this.errors);
                            });
                        }
                    });
                }
            })
            .catch((err)=>console.log(err));
        },
        addToCart(e){
            $(e.target).css('opacity', '.75').html('<span class="loading-circle "></span> ADDING...');
            axios.post('/api/action/'+this.form.productid+'/add-to-cart/'+this.form.qty)
            .then((res)=>{
                if (res.data.data.status == 200) {
                    console.log(res.data.data.cart);
                }
                $(e.target).css('opacity', '1').html('<i class="fas fa-shopping-cart"></i> ADD TO CART');
            })
            .catch((err)=>console.log(err));
        }

    }
}
</script>