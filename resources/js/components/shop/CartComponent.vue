<template>
    <div>
        <div class="wrapper">
            <div class='sb-text'>
                <h3><i class='fas fa-shopping-cart'></i> Cart</h3>
            </div>    
            <div v-if="carts.length <= 0" class="idb" style="padding: 0 .5rem 0;">
                <div class="shop-empty-col">
                    <img loading="lazy" :src="asset('assets/illustrations/empty_cart_animate.svg')" alt="Empty-Cart">
                    <div>
                        <p>You haven't added any product to Cart</p>Products added to Cart will appear here.<br>
                        <router-link :to="{name: 'shop', params:{page:'buy'}}"><i class="fas fa-search"></i> Find exciting Shops</router-link>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="raw.totalQty > 0">
            <div v-for="cart in carts" v-bind:key="cart.id" class="cart-seller-div">
                <div class="cart-profile-div">
                    <button type="submit" name="cart-delete" class="remove-from-cart remove-seller" @click="removeSeller(cart.seller_id, $event)">&times;</button>
                    <router-link :to="{name: 'visit', params: {username: cart.seller_name}, query:{v: 'shop'}}">
                        <img loading="lazy" :src="asset('storage/profile_images/'+cart.seller_image)" :alt="cart.seller_name">
                    </router-link>
                    <div class="cart-meta-div">
                        <p><i class="fas fa-user"></i> <b>{{cart.seller_name}}</b></p>
                        <p><i class="fas fa-map-marker-alt"></i> {{cart.seller_location}}</p>
                        <p><i class="fas fa-info-circle"></i> {{cart.seller_bio}}</p>
                        <button class="contact" @click="contactSeller(cart.seller_id, cart, $event)"><i class="fas fa-phone-alt"></i> CONTACT SELLER</button>
                    </div>
                </div>
                
                <div class="display-products">
                    <div v-for="product in cart.products" v-bind:key="product.id" class="product-div">
                        <div style="position:relative;">
                            <span class='product-category'>{{product.item.category}}</span>
                            <button type="submit" name="cart-delete" class="remove-from-cart remove-product" @click="removeProduct(product.item.id, product.item.user_id, $event)">&times;</button>
                        </div>
                        
                        <div class="img-col">
                            <span v-if="product.item.status == 1" class="product-status text-error">
                                Pre-Order
                            </span>
                            <span v-else-if="product.item.status == 2" class="product-status text-error">
                                Available
                            </span>
                            <span v-else-if="product.item.status == 3" class="product-status text-error">
                                Sold-Out
                            </span>
                            <span class="product-price alert-success">{{product.qty}} &times; ₦{{product.item.price}}</span>
                            <img loading="lazy" class="product-image" :src="asset('storage/product_images/'+product.item.image)" :alt="product.item.name"><br>
                        </div>
                        <div for="" class="min-descrip">
                            {{product.item.name.length > 10 ? product.item.name.substr(0,10)+'...' : product.item.name}}
                            <br>
                        </div>
                        <div class="btn-col">
                            <button type="button" name="button" class="modal-btn btn-success" @click="showModal(product.item.id)" style="font-size:.6rem"><i class="fas fa-ellipsis-h"></i></button>
                        </div>
                        <table style="display:none;">
                            <tr>
                                <td :class="'tablecategory_'+product.item.id">{{product.item.category}}</td>
                                <td v-if="product.item.status == 1" :class="'tablestatus_'+product.item.id">
                                    Pre-Order
                                </td>
                                <td v-else-if="product.item.status == 2" :class="'tablestatus_'+product.item.id">
                                    Available
                                </td>
                                <td v-else-if="product.item.status == 3" :class="'tablestatus_'+product.item.id">
                                    Sold-Out
                                </td>
                                <td :class="'tablename_'+product.item.id">{{product.item.name}}</td>
                                <td :class="'tableimg_'+product.item.id">{{asset('storage/product_images/'+product.item.image)}}</td>
                                <td :class="'tableprice_'+product.item.id">{{product.qty}} &times; {{product.item.price}}</td>
                                <td :class="'tabledescription_'+product.item.id" v-html="product.item.description"></td>
                                <td :class="'tablesellerimg_'+product.item.id">{{asset('storage/profile_images/'+product.item.seller_image)}}</td>
                                <td :class="'tableshopname_'+product.item.id">{{product.item.seller_name}}</td>
                                <td :class="'tablesellerid_'+product.item.id">{{product.item.user_id}}</td>
                                <td :class="'tableproductid_'+product.item.id">{{product.item.id}}</td>
                            </tr>
                        </table> 
                    </div>
                </div>
            </div>
            <div class="vs-wrapper" id="cart-footer">
                <div class="vs-container">
                    <div class="vs-content">
                        <a href="#l-div" class="share-scroll-down"></a>
                        <br>
                        <form action="#">
                            <div>
                                <h3>Total Products:</h3> {{raw.totalQty}}
                            </div>
                            <div>
                                <h3>Total Amount:</h3> ₦{{raw.totalPrice}}
                            </div>
                            <button type="button">EMPTY CART?</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="product-modal" class="modal">
            <div class="modal-content" style="border-radius:1rem;padding:.5rem;">
                <div class="modal-header">
                    <span class='modal-product-category'></span>
                    <span class="seller-id" style="display:none;"></span>
                    <span id="product-id" style="display:none;"></span>
                    <h3 class="modal-product-name sb-text"></h3>
                    <span class="close-modal closeBtn">&times;</span>
                </div>
                <div style="background: #f4f4f4;border-radius:1rem;padding: 0 .5rem .5rem;">
                    <div class="modal-product-content">
                        <div class="modal-image-col">
                            <div class="modal-image" id="render-block">
                                <span class="modal-product-status">
                                </span>
                                <span class="modal-product-price"></span>
                                <img loading="lazy" src="" alt="product-image" style="
                                min-width: 130px;"/>
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
                                            <input type="number" class="modal-product-qty" name="modal-product-qty" id="order-qty" :disabled="$route.params.page == 'cart'" v-model="form.qty">
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
                            <div class="modal-footer-container">
                                <div class="wishlist-btn-col" v-if="showWishlist">
                                    <button v-if="!addWishlist" type="button" name="button" class="rmv-wishlist" @click="removeFromWishlist(form.productid)"><i class="far fa-bookmark"></i></button>
                                    <button v-else type="button" name="button" @click="addToWishlist(form.productid)" class="wishlist"><i class="far fa-bookmark"></i></button>
                                </div>
                                <div v-else>

                                </div>
                                <div class="modal-footer-btn" v-if="$route.params.page != 'inventory'">
                                    <button type="button" name="button" class="closeBtn ">Close</button>
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
import ProductsComponent from './inc/ProductsComponent';
export default {
    data() {
        return {
            carts: [],
            raw:{},
            form: {
                productid: '',
                action: '',
                sellername: '',
                sellerid: '',
                sellerimgsource: '',
                page: '',
                qty: 1,
            },
            showWishlist: '',
            addWishlist: '',
        }
    },
    components:{
        ProductsComponent
    },
    props: ["user"],
    created() {
        axios.get('/api/shop/cart')
        .then(res =>{
            if (res.data.data.status == 200) {
                this.carts = res.data.data.shopping_cart.filter;
                this.raw = res.data.data.shopping_cart;
                console.log(this.raw);
            }
        }).catch(err=>console.log(err));
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
        showModal(id){
            for (const key in this.carts) {
                if (Object.hasOwnProperty.call(this.carts, key)) {
                    const element = this.carts[key];
                    element.products.forEach(product => {
                        if(product.item.id == id){
                            this.form.qty = product.qty;
                        }
                    });
                    
                }
            }
            $(".seller-id").text($(".tablesellerid_" + id).text());
            this.form.productid = $(".tableproductid_" + id).text();
            this.form.action = "update-view";
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
                    for (const key in this.carts) {
                        if (Object.hasOwnProperty.call(this.carts, key)) {
                            const element = this.carts[key];
                            element.products.forEach(product => {
                                if(product.item.id == id && product.item.in_wishlist == true){
                                    this.addWishlist = false;
                                }else if(product.item.id == id && product.item.in_wishlist == false){
                                    this.addWishlist = true;
                                }
                            });
                            
                        }
                    }
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

            $(".seller-name").text(this.form.sellername);
            $("#product-id").text($(".tableproductid_" + id).text());
            $(".social-links").html("<a href='https://api.whatsapp.com/send?phone=&text=Guys! I just found an awesome product on ```FUOYE360 SHOP```. Check it out on https://fuoye360.com/shop/buy?pid=" + id + "' target='_blank'><i class='fab fa-whatsapp'></i></a> <a href='https://twitter.com/intent/tweet?text=Guys! I just found an awesome product on FUOYE360 SHOP. Check it out on https://fuoye360.com/shop/buy?pid=" + id + " %23fuoye360%23fuoye360shop' target='_blank'><i class='fab fa-twitter'></i></a>");
            setTimeout(() => {
                $("#product-modal").show();
            }, 800); 
        },
        closeModal(){
            $("#product-modal").hide();
            this.form.qty = 1;        
        },
        addToWishlist(id){
            axios.post('/api/action/'+id+'/add-to-wishlist')
            .then((res)=>{
                if (res.data.data.status == 200) {
                    for (const key in this.carts) {
                        if (Object.hasOwnProperty.call(this.carts, key)) {
                            const element = this.carts[key];
                            element.products.forEach(product => {
                                if(product.item.id == id){
                                    product.item.in_wishlist == true;
                                    this.addWishlist = false;
                                }
                            });
                            
                        }
                    }
                }
            })
            .catch((err)=>console.log(err));
        },
        removeFromWishlist(id){
            axios.post('/api/action/'+id+'/remove-from-wishlist')
            .then((res)=>{
                if (res.data.data.status == 200) {
                    for (const key in this.carts) {
                        if (Object.hasOwnProperty.call(this.carts, key)) {
                            const element = this.carts[key];
                            element.products.forEach(product => {
                                if(product.item.id == id){
                                    product.item.in_wishlist == false;
                                    this.addWishlist = true;
                                } 
                            });
                        }
                    }
                }
            })
            .catch((err)=>console.log(err));
        },
        removeSeller(id, e){
            $(e.target).css('opacity', '.75').html('<span class="loading-circle "></span>');
            axios.post('/api/action/'+id+'/remove-seller-from-cart')
            .then((res) =>{
                if (res.data.data.status == 200) {
                    $(e.target).css('opacity', '1').html('&times;');
                    this.carts = res.data.data.shopping_cart.filter;
                    this.raw = res.data.data.shopping_cart;
                    console.log(this.raw);
                }

            })
            .catch(err =>console.log(err));
        },
        removeProduct(id, sid, e){
            $(e.target).css('opacity', '.75').html('<span class="loading-circle "></span>');
            axios.post('/api/action/'+id+'/remove-product-from-cart/'+sid)
            .then((res) =>{
                if (res.data.data.status == 200) {
                    $(e.target).css('opacity', '1').html('&times;');
                    this.carts = res.data.data.shopping_cart.filter;
                    this.raw = res.data.data.shopping_cart;
                    console.log(this.raw);
                }

            })
            .catch(err =>console.log(err));
        },
        contactSeller(id, cart, e){
            $(e.target).css('opacity', '.75').html('<span class="loading-circle "></span> CONTACTING...');
            axios.post('/api/action/'+id+'/contact-seller')
            .then(res=>{
                $(e.target).css('opacity', '.75').html('CONTACT SELLER');
                if (res.data.data.status == 200) {
                    console.log(res.data.data.shopping_cart.checkout);
                    this.carts = res.data.data.shopping_cart.filter;
                    this.raw = res.data.data.shopping_cart;
                    console.log(cart.products);
                    let text = '';
                    cart.products.forEach((element, index)=>{
                        let item = '%0A%0A *'+element.qty + ' '+ element.item.name+'* %0A%0A';
                        text+= item;
                    });

                    console.log(text);
                    // let unicode = 0x1F600
                    // let s = new StringBuilder();
                    // s.append("YOUR MESSAGE HERE");
                    // s.append(Character.toChars(unicode));
                    
                    let url = "https://wa.me/" + cart.seller_telephone + "?text=```MESSAGE FROM FUOYE360 SHOP```%0A%0AHi! ```" + cart.seller_name + "👋```%0A%0AThis is *" + this.user.name + "* from ```FUOYE360 SHOP``` and I'd like to buy:" + text + ".%0A%0A```Thanks.```"

                    window.open(url, '_blank');
                }
            })
            .catch(err=>console.log(err));
            console.log(this.carts);
        }
    }
}
</script>

<style scoped>
    .cart-profile-div{
        background: var(--white-color);
        padding: .5rem;
        margin: .5rem;
        border-radius: 1rem;
        display: grid;
        grid-template-columns: 30% 70%;
        align-items: center;
        position: relative;
    }
    .cart-profile-div .cart-meta-div{
        display: grid;
    }
    .cart-profile-div .cart-meta-div p{
        display: flex;
        align-items: center;
        word-break: break-all;

    }
    .cart-profile-div i{
        color: var(--brand-color);
        margin-right: .5rem;
    }
    .cart-profile-div .contact{
        margin-top: 1rem;
        justify-self: flex-end;
        width: 100%;
        max-width: 320px;
        padding: .3em;
        box-shadow: 0 0 2px rgba(0, 0, 0, .15);
        color: var(--brand-color);
        /* color: var(--white-color); */
        font-weight: bold;
        text-transform: uppercase;
        cursor: pointer;
        outline-color: gold;
        border: none;
        border-radius: 1rem;
    }
    .cart-profile-div .contact i{
        color: inherit;
    }
    .cart-profile-div a{
        justify-self: center;
    }

    .cart-profile-div img{
        width: 75px;
        height: 75px;
        border-radius: 50%;
        object-fit: cover;
        margin: 0 auto;
    }
    .cart-seller-div{
        margin-bottom: 2rem;
    }
    .cart-seller-div .display-products{
        max-width: 100vw;
        display: block;
        padding: .2rem 1rem;
        white-space: nowrap;
        overflow-x: scroll;
        overflow-y: hidden;
        white-space: nowrap;

    }
    .cart-seller-div .product-div{
        display: inline-block;
        margin-right: 1rem;
    }
    .remove-from-cart{
        border: none;
        margin: 0 -3px 0 0;
        position: absolute;
        background: none;
        font-size: 1.7rem;
        color:red;
        cursor: pointer;
        box-shadow: none;
    }
    .remove-product{
        top: 0;
        right: 0;
    }
    .remove-seller{
        top: 5px;
        left: 5px;    
    }
    #cart-footer{
        padding-top: 160px;
        background: none;
        z-index: 0;
        display: none;
    }
    #cart-footer .vs-container{
        margin: 0;
        padding: 0;
        height: 100vh;
        display: flex;
        align-items: flex-end;
        justify-content: center;
    }
    #cart-footer .vs-content{
        box-shadow: 0 0 4px 2px rgba(0, 0, 0, .15);
    }
    #cart-footer h3{
        display: inline-block;
    }

    #cart-footer form{
        padding: 1rem;
    }
    #cart-footer button{
        margin-top: 1rem;
        padding: .5rem;
        border-radius: .5rem;
        border: 2px solid var(--brand-color);
        color: var(--brand-color);
        background: var(--white-color);
        font-weight: bold;
    }
</style>