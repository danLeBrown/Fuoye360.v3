<template>
    <div>
        <div v-if="$route.params.page == 'cart'">
            <cart-component  v-bind:user="user" v-on:viewImage="viewImage"></cart-component>
        </div>
        <div v-else>
            <div class="wrapper">
                <!-- INDEX PAGE -->
                <div v-if="$route.params.page == undefined" >
                    <div class="h-trendn-b">
                        <h3 class="sb-text">
                            <i class="fas fa-fire"></i> Trending Shops
                        </h3>
                    </div>
                    <div class="inset-div">
                        <div class="trend-b">
                            <div v-for="trending in trendings" v-bind:key="trending.id" class="trendn-col visit-acct" @click="visitUser($event, trending.name)">
                                <button class="trendn-help" :class="'btnid_'+trending.id" @click="viewShopInfo(trending.id, $event)" style="display:block;"><i class="fas fa-info-circle"></i></button>
                                <div class="sub-div">
                                    <div class="shop-img">
                                        <img loading="lazy" :src="asset('storage/profile_images/'+trending.image)" :alt="trending.name">
                                    </div>
                                    <div class="trendn-info">
                                        <table style="display:none">
                                            <td :class="'trendn_name_'+trending.id">{{trending.name}}</td>
                                            <td :class="'trendn_location_'+trending.id">{{trending.geo_location}}</td>
                                            <td :class="'trendn_followers_'+trending.id">{{trending.followers}}</td>
                                            <td :class="'trendn_following_'+trending.id">{{trending.following}}</td>
                                            <td :class="'trendn_views_'+trending.id"><i class="fas fa-eye"></i> {{trending.total_views}}</td>
                                        </table>
                                        <p class="" style="font-weight:bold;">
                                            {{trending.name.length > 10 ? trending.name.substr(0,10)+'...' : trending.name}}

                                            <!-- {{trending.name}} -->
                                        </p>
                                        <div :class="'btndiv_'+trending.id">
                                            <div v-if="user.id == trending.id" class="s-text">Trending!! <i class="fas fa-fire"></i></div>
                                            <button v-else-if="trending.is_following == true" type="button" name="button" :class="'seller-unfollow-btn btnid_'+trending.id" @click="unflwUser(trending.id, trending.name, $route.params.page, $event)"> Unfollow</button>
                                            <button v-else type="button" name="button" :class="'seller-follow-btn btnid_'+trending.id" @click="flwUser(trending.id, trending.name, $route.params.page, $event)" :data-id="trending.id"> Follow</button>                                        
                                    </div>
                                    </div>
                                </div>
                                <div class="trending-info-div" style="position: absolute; display:none;">
                                    <div>
                                        <i class="fas fa-user"></i> {{trending.name}}
                                    </div>
                                    <div>
                                        <i class="fas fa-map-marker-alt"></i> {{trending.geo_location}}
                                    </div>
                                    <div>
                                        <i class="fas fa-info-circle"></i>
                                    </div>
                                    <div>
                                        {{trending.following}} Following {{trending.followers}} Followers
                                    </div>
                                    <div>
                                        <i class="fas fa-eye"></i> {{trending.total_views}} views
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>

                <!-- WISHLIST PAGE -->
                <div v-else-if="$route.params.page == 'wishlist'">
                    <div class='sb-text'>
                        <h3><i class='fas fa-bookmark'></i> Wishlist</h3>
                    </div>            
                </div>

                <!-- INVENTORY PAGE -->
                <div v-else-if="$route.params.page == 'inventory'">
                    <div class='sb-text inventory-h'>
                        <h3><i class='fas fa-store'></i> Inventory</h3>
                        <button id="help" @click="showInventoryHelp"><i class="fas fa-question-circle"></i> help</button>
                    </div>
                    <div class="vs-wrapper show-help-col" @click="closeModal($event)">
                        <div class="vs-container">
                            <div class="vs-content show-help">
                                <span class="close" @click="closeThisModal($event)">&times;</span>
                                <h4 class="sb-text" style="border:none;">Welcome to <i class="fas fa-store"></i> INVENTORY?</h4>
                                <div style="background: #f4f4f4;border-radius:1rem;padding:0 .25rem;">
                                    <div class="help-div">
                                        <p>Your Inventory is your store where you can see all your products and services either availble for sales or not and make changes to them as you see fit.</p>
                                    </div>
                                    <div class="help-div help-div-2">
                                        <h4 class="sb-text" style="border-radius: 1rem; border:none;">What should I know <i class="fas fa-question"></i></h4>
                                        <p><span class="modal-views"><i class="fas fa-eye"></i> Views</span> Views are <span style="font-weight:bold;color:gold;">GOLDEN</span>. They show you the number of times your product /service has been visited by potential buyers. Thus, the higher the views on your product, the more chances for you to make sales</p>
                                        <p><span class="modal-btn-delete"><i class="fas fa-trash"></i> Delete</span> This deletes your Product/ Service. Take note though, what is dead may never die.</p>
                                        <p><span class="modal-btn-success"><i class="fas fa-edit"></i> Edit</span> This enables you to make updates to your Product/ Service</p>
                                        <p><span class="modal-btn-success"><i class="fas fa-check"></i> Update</span> This applies the updates you've made to your Product/ Service</p>
                                    </div>
                                    <div class="help-div help-div-last">
                                        With <i class="fas fa-heart"></i> from Brown.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DELETE AFFIRMATION -->
                    <div class="vs-wrapper delete-alert-col-hide" @click="closeModal($event)">
                        <div class="vs-container" style="margin: 0; padding:0; height:100vh;display:flex;align-items:flex-end;justify-content:center;">
                            <div class="vs-content" style="border-radius: 1rem 1rem 0 0;position: relative;">
                                <a href="#l-div" class="share-scroll-down"></a>
                                <h3 style="text-align:center;margin:1rem 0">Are you Sure?</h3>
                                <form action="" method="post" style="border-radius: 1rem 1rem 0 0;display:flex;padding:1rem;margin-top:1rem;" class="delete-affirmation">
                                    <button type="button" name="delete" class="delete" value="0" @click="closeThisModal">No</button>
                                    <button type="button" name="delete" value="1" class="delete delete-affirm" @click="deleteProduct">Yes</button>
                                </form>
                            </div>
                        </div>

                    </div>
                    <!-- <div class="delete-alert-col delete-alert-col-hide">
                        <div class="delete-affirmation">
                            Are you sure?
                        </div>
                    </div> -->

                    <!-- SELL BUTTON -->
                    <div class="sell-btn-div">
                        <button @click="showSellModal"><i class="fas fa-plus"></i></button>
                    </div>
                    <!-- <div class="vs-wrapper">
                        <div class="vs-container">
                            <div class="vs-content">
                                
                            </div>
                        </div>
                    </div> -->
                    <paystack
                        :amount="amount"
                        :email="email"
                        :paystackkey="paystackkey"
                        :reference="reference"
                        :callback="callback"
                        :close="close"
                        :embed="false"
                        style="display:none"
                    >
                    <i class="fas fa-money-bill-alt"></i>
                    Make Payment
                    </paystack>

                    <!-- SELL MODAL -->
                    <div class="vs-wrapper" id="sell-modal" @click="closeModal($event)">
                        <div class="vs-container">
                            <div class="vs-content" style="max-width:none;">
                                <a href="#l-div" class="share-scroll-down"></a>
                                <button class="close-sell-modal close-btn" @click="closeThisModal($event)">&times;</button>
                                <br>
                                <div class="product-info">
                                    <div class="form-h">                                             
                                        <i class='fas fa-info-circle'></i> FILL IN PRODUCT INFO
                                    </div><br>
                                    <form action="" class="add-to-inventory" @submit.prevent="addToInventory($event)">
                                        <div class="form-product-div">
                                            <div class="product-img-col">
                                                <img loading="lazy" :src="asset('assets/images/productimage.jpg')" alt='product-image' @click="triggerclick" id="product-image-display"/>
                                                <input type="file" name="image" accept=".jpeg, .jpg, .png" id="" style="display:none;" class="product-sell-image" @change="displayImage($event)">
                                            </div>
                                            <div class="form-product-input">
                                                <div class="form-product-irow">
                                                    <label for="name" class="success">Name</label>
                                                    <input type="text" name="name" id="name" placeholder="product | service name" maxlength="21" required>
                                                </div>
                                                <div class="form-product-irow">
                                                    <label for="category" class="success">Category</label>
                                                    <select name="category" id="category" required>
                                                        <option>Pick a Category</option>
                                                        <option value="arts">Arts</option>
                                                        <option value="computing">Computing</option>
                                                        <option value="education">Education &amp; Books</option>
                                                        <option value="ecomponents">Electrical Equipments &amp; Components</option>
                                                        <option value="electronics">Electronics</option>
                                                        <option value="fashion" >Fashion</option>
                                                        <option value="food" >Food</option>
                                                        <option value="furniture" >Furniture</option>
                                                        <option value="gifts" >Gift Items</option>
                                                        <option value="health" >Health &amp; Beauty</option>
                                                        <option value="housing" >Housing</option>
                                                        <option value="music" >Musical Instruments &amp; Equipments</option>
                                                        <option value="phones" >Phones &amp; Tablets</option>
                                                        <option value="services" >Services</option>
                                                        <option value="stationaries" >Stationaries</option>
                                                        <option value="sports" >Sports &amp; Outdoor</option>
                                                        <option value="transport" >Transportation</option>
                                                        <option value="uniforms" >Uniforms (P.P.E)</option>
                                                        <option value="others" >Others</option>
                                                    </select>
                                                </div>
                                                <div class="form-product-irow">
                                                    <label for="status" class="success">Status</label>
                                                    <select name="status" id="product-qty" required>
                                                        <option value="1">Pre-Order</option>
                                                        <option value="2">Available</option>
                                                    </select>
                                                </div>
                                                <div class="form-product-irow">
                                                    <label for="price" class="success">Price</label>
                                                    <input type="number" placeholder="" required name="price">
                                                </div>
                                                <div class="form-product-irow">
                                                    <label for="tags" class="success">Tags</label>
                                                    <input type="text" placeholder="#" name="tags">
                                                    <br>
                                                </div>
                                                <div class="form-product-desc-div">
                                                    <label for="description" class="success product-info-desc">Description</label>
                                                    <textarea name="description" id="description" maxlength="200" wrap="soft" required></textarea>
                                                </div>
                                                <div class="sell-btn">
                                                    <button type="button" class="close close-btn" @click="closeThisModal">CLOSE</button>
                                                    <button type="submit" v-if="user.products_lifetime <= 0" class="submit-product">FREE SLOT</button>
                                                    <paystack
                                                        type="button"
                                                        v-else-if="user.products_lifetime >= 3 && user.payment_verified == 0"
                                                        :amount="amount"
                                                        :email="email"
                                                        :paystackkey="paystackkey"
                                                        :reference="reference"
                                                        :callback="callback"
                                                        :close="close"
                                                        :embed="false"
                                                        class="submit-product"
                                                    >
                                                    <i class="fas fa-money-bill-alt"></i>
                                                        PAY 500
                                                    </paystack>
                                                    <button v-else type="submit" class="submit-product">SELL</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="$route.params.page == 'buy'">
                    <div style="text-align:center;">
                        <button class='browse-shop sb-text' style="width: 100%; max-width:425px;" @click="showCategory">
                        <i class='fas fa-search'></i> Browse Category
                        <i class="fas fa-angle-down"></i>
                        </button>
                    </div>
                    <div>   
                        <div class="category-col">
                            <router-link :to="{name: 'shop', params:{page: 'buy'}, query:{category: 'arts'}}" :class="$route.query.category != 'arts'? 'cat-nselected': 'cat-selected'"><i class="fas fa-palette"></i><br>Arts</router-link>
                            <router-link :to="{name: 'shop', params:{page: 'buy'}, query:{category: 'computing'}}" :class="$route.query.category != 'computing'? 'cat-nselected': 'cat-selected'"><i class="fas fa-keyboard"></i><br>Computing</router-link>
                            <router-link :to="{name: 'shop', params:{page: 'buy'}, query:{category: 'education'}}" :class="$route.query.category != 'education'? 'cat-nselected': 'cat-selected'"><i class="fas fa-book"></i><br>Education & Books</router-link>
                            <router-link :to="{name: 'shop', params:{page: 'buy'}, query:{category: 'ecomponents'}}" :class="$route.query.category != 'ecomponents'? 'cat-nselected': 'cat-selected'"><i class="fas fa-microchip"></i><br>Electrical Components & Equipments</router-link>
                            <router-link :to="{name: 'shop', params:{page: 'buy'}, query:{category: 'electronics'}}" :class="$route.query.category != 'electronics'? 'cat-nselected': 'cat-selected'"><i class="fas fa-tv"></i><br>Electronics</router-link>
                            <router-link :to="{name: 'shop', params:{page: 'buy'}, query:{category: 'fashion'}}" :class="$route.query.category != 'fashion'? 'cat-nselected': 'cat-selected'"><i class="fas fa-tshirt"></i><br>Fashion</router-link>
                            <router-link :to="{name: 'shop', params:{page: 'buy'}, query:{category: 'food'}}" :class="$route.query.category != 'food'? 'cat-nselected': 'cat-selected'"><i class="fas fa-utensils"></i><br>Food</router-link>
                            <router-link :to="{name: 'shop', params:{page: 'buy'}, query:{category: 'furniture'}}" :class="$route.query.category != 'furniture'? 'cat-nselected': 'cat-selected'"><i class="fas fa-chair"></i><br>Furniture</router-link>
                            <router-link :to="{name: 'shop', params:{page: 'buy'}, query:{category: 'gifts'}}" :class="$route.query.category != 'gifts'? 'cat-nselected': 'cat-selected'"><i class="fas fa-gifts"></i><br>Gift Items</router-link>
                            <router-link :to="{name: 'shop', params:{page: 'buy'}, query:{category: 'health'}}" :class="$route.query.category != 'health'? 'cat-nselected': 'cat-selected'"><i class="fas fa-stethoscope"></i><br>Health & Beauty</router-link>
                            <router-link :to="{name: 'shop', params:{page: 'buy'}, query:{category: 'housing'}}" :class="$route.query.category != 'housing'? 'cat-nselected': 'cat-selected'"><i class="fas fa-home"></i><br>Housing</router-link>
                            <router-link :to="{name: 'shop', params:{page: 'buy'}, query:{category: 'music'}}" :class="$route.query.category != 'music'? 'cat-nselected': 'cat-selected'"><i class="fas fa-music"></i><br>Musical Instruments & Equipments</router-link>
                            <router-link :to="{name: 'shop', params:{page: 'buy'}, query:{category: 'phones'}}" :class="$route.query.category != 'phones'? 'cat-nselected': 'cat-selected'"><i class="fas fa-tablet"></i><br>Phones & Tablets</router-link>
                            <router-link :to="{name: 'shop', params:{page: 'buy'}, query:{category: 'services'}}" :class="$route.query.category != 'services'? 'cat-nselected': 'cat-selected'"><i class="fas fa-handshake"></i><br>Services & Jobs</router-link>
                            <router-link :to="{name: 'shop', params:{page: 'buy'}, query:{category: 'stationaries'}}" :class="$route.query.category != 'stationaries'? 'cat-nselected': 'cat-selected'"><i class="fas fa-calculator"></i><br>Stationaries</router-link>
                            <router-link :to="{name: 'shop', params:{page: 'buy'}, query:{category: 'sports'}}" :class="$route.query.category != 'sports'? 'cat-nselected': 'cat-selected'"><i class="fas fa-futbol"></i><br>Sports & Outdoor</router-link>
                            <router-link :to="{name: 'shop', params:{page: 'buy'}, query:{category: 'transport'}}" :class="$route.query.category != 'transport'? 'cat-nselected': 'cat-selected'"><i class="fas fa-car"></i><br>Transportation</router-link>
                            <router-link :to="{name: 'shop', params:{page: 'buy'}, query:{category: 'uniforms'}}" :class="$route.query.category != 'uniforms'? 'cat-nselected': 'cat-selected'"><i class="fas fa-user-tie"></i><br>Uniforms (P.P.E)</router-link>
                            <router-link :to="{name: 'shop', params:{page: 'buy'}, query:{category: 'others'}}" :class="$route.query.category != 'others'? 'cat-nselected': 'cat-selected'"><i class="fas fa-ellipsis-h"></i><br>Others</router-link>
                        </div>
                        <div class="shop-empty-col" v-if="showSvg">
                            <img loading="lazy" :src="asset('assets/illustrations/easter-egg-hunt-animate.svg')" alt="">
                            <div>
                                <p>You're not browsing any CATEGORY currently.</p>
                            </div>
                        </div>
                    </div><br>
                    <div class="dynamic-products-block" id="page-header">
                        <h3 class='product-header sb-text' style="border: none;" v-html="pageHeader">
                            
                        </h3>
                    </div>
                </div>

                <div v-if="products.length <= 0">
                    <div>
                        <div class="shop-empty-col" v-if="$route.params.page == undefined">
                            <img loading="lazy" :src="asset('assets/illustrations/example-animate.svg')" alt="">
                            <div>
                                <p>Wow! You're not following any shop yet.</p>
                                Products and services offered by shops you follow will appear here.
                                <router-link :to="{name: 'shop', params:{page:'buy'}}"><i class="fas fa-search"></i> Find exciting Shops</router-link>
                            </div>
                        </div>
                        <div class="shop-empty-col" v-else-if="$route.params.page == 'wishlist'">
                            <img loading="lazy" :src="asset('assets/illustrations/reading-list-animate.svg')" alt="Empty-Wishlist">
                            <div>
                                <p>Oops! You haven't added any product to your Wishlist.</p> 
                                Products added to Wishlist will appear here.
                                <router-link :to="{name: 'shop', params:{page:'buy'}}"><i class="fas fa-search"></i> Find exciting Shops</router-link>
                            </div>            
                        </div>
                        <div class="shop-empty-col" v-else-if="$route.params.page == 'inventory'">
                            <img loading="lazy" :src="asset('assets/illustrations/opened-animate.svg')" alt="">
                            <div><p>Oops! You have no product yet. </p>
                            Products you're selling will appear here.
                            <router-link :to="{name: 'shop', params:{page:'buy'}}"><i class="fas fa-search"></i> Find exciting Shops</router-link>
                            </div>           
                        </div>
                        <div class="shop-empty-col" v-else-if="$route.params.page == 'buy' && $route.query.category != ''">
                            <img loading="lazy" :src="asset('assets/illustrations/easter-egg-hunt-animate.svg')" alt="">
                            <div><p>Oops! No product under this Category yet. </p>
                            <router-link :to="{name: 'shop', params:{page:'inventory'}, query:{category: $route.query.category}}">Be the first to sell under this category</router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="products.length && products.length > 0">
                <products-component v-bind:products="products" v-bind:user="user" v-on:updateWishlist="updateWishlist" v-on:confirmDelete="confirmDelete"></products-component>
                <infinite-loading @infinite="infiniteHandler"></infinite-loading>
            </div>

        </div>

    </div>
</template>
<script>
import paystack from 'vue-paystack';
import ProductsComponent from '../components/shop/inc/ProductsComponent';
import CartComponent from '../components/shop/CartComponent';
import InfiniteLoading from 'vue-infinite-loading';

export default {
    data() {
        return {
            api: '',
            products: [],
            errors:[],
            page: 1,
            trendings:[],
            newProducts:[],
            filterProducts: [],
            pageHeader: '',
            showSvg: true,
            product: {},
            paystackkey: "pk_live_6fc4307b61b69baa2c77d83386769c5eaab9c478", //paystack public key
            amount: 50000, // in kobo,
            email: "ayomidedaniel@g.com"
        }
    },
    props: ["user"],
    components:{
        ProductsComponent,
        InfiniteLoading,
        CartComponent,
        paystack
    },
    computed: {
      reference(){
        let text = "";
        let possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        for( let i=0; i < 10; i++ )
          text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
      }
    },
    watch: {
        $route(){
            this.page = 1;
            this.api = '';
            this.products = [];
            const current_page = this.$route.params.page;
            if(current_page == undefined){
                this.getTrending();
                this.api = '/api/shop';
            }else if (current_page == "inventory") {
                // console.log(this.user);
                this.api = '/api/shop/inventory';
            }else if (current_page == "wishlist") {
                this.api = '/api/shop/wishlist';
            }else if (current_page == "buy") {
                if (['arts', 'computing', 'education', 'ecomponents', 'electronics', 'fashion', 'food', 'furniture', 'gifts', 'health', 'housing', 'music', 'phones', 'services', 'stationaries', 'sports', 'transport', 'uniforms', 'others'].includes(this.$route.query.category)) {
                    this.showSvg = false;
                    let category = this.$route.query.category;
                    if(category == 'arts'){
                        this.pageHeader = '<i class="fas fa-palette"></i> Arts' ;
                    }else if (category == 'computing'){
                        this.pageHeader =  '<i class="fas fa-keyboard"></i> Computing';
                    }else if (category == 'education'){
                        this.pageHeader = '<i class="fas fa-book"></i> Education & Books';
                    }else if (category == 'ecomponents'){
                        this.pageHeader = '<i class="fas fa-microchip"></i> Electrical Components & Equipments';
                    }else if (category == 'electronics'){
                        this.pageHeader = '<i class="fas fa-tv"></i> Electronics';
                    }else if (category == 'fashion'){
                        this.pageHeader = '<i class="fas fa-tshirt"></i> Fashion';
                    }else if (category == 'food'){
                        this.pageHeader = '<i class="fas fa-utensils"></i> Food';
                    }else if (category == 'furniture'){
                        this.pageHeader = '<i class="fas fa-chair"></i> Furniture';
                    }else if (category == 'gifts'){
                        this.pageHeader = '<i class="fas fa-gifts"></i> Gift Items';
                    }else if (category == 'health'){
                        this.pageHeader = '<i class="fas fa-stethoscope"></i> Health & Beauty';
                    }else if (category == 'housing'){
                        this.pageHeader = '<i class="fas fa-home"></i> Housing';
                    }else if (category == 'music'){
                        this.pageHeader = '<i class="fas fa-music"></i> Musical Instruments & Equipments';
                    }else if (category == 'phones'){
                        this.pageHeader = '<i class="fas fa-tablet"></i> Phones & Tablets';
                    }else if (category == 'services'){
                        this.pageHeader = '<i class="fas fa-handshake"></i> Services & Jobs';
                    }else if (category == 'stationaries'){
                        this.pageHeader = '<i class="fas fa-calculator"></i> Stationaries';
                    }else if (category == 'sports'){
                        this.pageHeader = '<i class="fas fa-futbol"></i> Sports & Outdoor';
                    }else if (category == 'transport'){
                        this.pageHeader = '<i class="fas fa-car"></i> Transportation';
                    }else if (category == 'uniforms'){
                        this.pageHeader = '<i class="fas fa-user-tie"></i> Uniforms (P.P.E)';
                    }else if (category == 'others'){
                        this.pageHeader = '<i class="fas fa-ellipsis-h"></i> Others';
                    }
                    $(".category-col .cat-nselected").slideToggle();
                    this.api = '/api/shop/buy/'+ this.$route.query.category; 
                }else{
                    this.showSvg = true;
                    this.pageHeader = '<i class="fas fa-shopping-bag"></i> Products & Services <i class= "fas fa-handshake"></i>';
                    this.api = '/api/shop/buy/';
                }            
            }
            this.infiniteHandler();
        
        }
    },
    mounted() {
        /**
         * FOR INVENTORY PAGE
         */
        $(".show-help .close").on('click', function() {
            $(".show-help, .show-help-col").fadeOut();
        });
        
        $("#sell-modal").click(function (e) {
            if ($(e.target).closest(".vs-content").length == 0) {
                $("#sell-modal").fadeOut();
            }
        });
        
        $(".close-btn").click(function (e) {
            $("#sell-modal").fadeOut();
        });
    },
    created() {
        const current_page = this.$route.params.page;
        if(current_page == undefined){
            this.getTrending();
            this.api = '/api/shop';
        }else if (current_page == "inventory") {
            // console.log(this.user);
            this.api = '/api/shop/inventory';
        }else if (current_page == "wishlist") {
            this.api = '/api/shop/wishlist';
        }else if (current_page == "buy") {
            if (['arts', 'computing', 'education', 'ecomponents', 'electronics', 'fashion', 'food', 'furniture', 'gifts', 'health', 'housing', 'music', 'phones', 'services', 'stationaries', 'sports', 'transport', 'uniforms', 'others'].includes(this.$route.query.category)) {
                this.showSvg = false;
                let category = this.$route.query.category;
                if(category == 'arts'){
                    this.pageHeader = '<i class="fas fa-palette"></i> Arts' ;
                }else if (category == 'computing'){
                    this.pageHeader =  '<i class="fas fa-keyboard"></i> Computing';
                }else if (category == 'education'){
                    this.pageHeader = '<i class="fas fa-book"></i> Education & Books';
                }else if (category == 'ecomponents'){
                    this.pageHeader = '<i class="fas fa-microchip"></i> Electrical Components & Equipments';
                }else if (category == 'electronics'){
                    this.pageHeader = '<i class="fas fa-tv"></i> Electronics';
                }else if (category == 'fashion'){
                    this.pageHeader = '<i class="fas fa-tshirt"></i> Fashion';
                }else if (category == 'food'){
                    this.pageHeader = '<i class="fas fa-utensils"></i> Food';
                }else if (category == 'furniture'){
                    this.pageHeader = '<i class="fas fa-chair"></i> Furniture';
                }else if (category == 'gifts'){
                    this.pageHeader = '<i class="fas fa-gifts"></i> Gift Items';
                }else if (category == 'health'){
                    this.pageHeader = '<i class="fas fa-stethoscope"></i> Health & Beauty';
                }else if (category == 'housing'){
                    this.pageHeader = '<i class="fas fa-home"></i> Housing';
                }else if (category == 'music'){
                    this.pageHeader = '<i class="fas fa-music"></i> Musical Instruments & Equipments';
                }else if (category == 'phones'){
                    this.pageHeader = '<i class="fas fa-tablet"></i> Phones & Tablets';
                }else if (category == 'services'){
                    this.pageHeader = '<i class="fas fa-handshake"></i> Services & Jobs';
                }else if (category == 'stationaries'){
                    this.pageHeader = '<i class="fas fa-calculator"></i> Stationaries';
                }else if (category == 'sports'){
                    this.pageHeader = '<i class="fas fa-futbol"></i> Sports & Outdoor';
                }else if (category == 'transport'){
                    this.pageHeader = '<i class="fas fa-car"></i> Transportation';
                }else if (category == 'uniforms'){
                    this.pageHeader = '<i class="fas fa-user-tie"></i> Uniforms (P.P.E)';
                }else if (category == 'others'){
                    this.pageHeader = '<i class="fas fa-ellipsis-h"></i> Others';
                }
                this.api = '/api/shop/buy/'+ this.$route.query.category; 
                $(".category-col .cat-nselected").slideToggle();
            }else{
                this.showSvg = true;
                this.pageHeader = '<i class="fas fa-shopping-bag"></i> Products & Services <i class= "fas fa-handshake"></i>';
                this.api = '/api/shop/buy/';
            }            
        }
        this.infiniteHandler();
    },
    mounted() {
    
    },
    methods: {
        /**
            PAYSTACK METHODS
         */
        callback: function(response){
            console.log(response)
        },
        close: function(){
            console.log("Payment closed")
        },
        confirmDelete(data){
            this.product = data;
            $('.delete-alert-col-hide').show();
        },
        deleteProduct(){
            return false;
        },
        updateUser(data){
            this.$emit('updateUser', data);
        },
        viewImage(src){
            this.$emit('viewImage', src);
        },
        showCategory(){
            $(".category-col .cat-nselected").slideToggle();
        },
        async getTrending(){
            await axios.get('/api/shop/trending')
            .then(res=>{
                this.trendings = res.data.data;
            })
            .catch(err=>console.log(err));
        },
        async infiniteHandler($state) {
            await axios.get(this.api, {
                params: {
                page: this.page,
                },
            }).then((res) => {
                console.log(res);
                if (res.data.data.length) {
                    this.page += 1;
                    this.products.push(...res.data.data);
                    $state.loaded();
                } else {
                    $state.complete();
                }
            })
            .catch(err=>console.log(err));
        },
        updateWishlist(data){
            this.products = data.products;
        },
        async flwUser(id, name, page, event){
            await axios.post('/api/account/'+id+'/follow')
            .then((res)=>{
                console.log(res.data.data);
                if (res.data.data.status == 200) {
                    this.$emit('updateUser', {user: {
                        following : res.data.data.sender_count
                    }});
                    axios.get('/api/shop/'+id)
                    .then((res)=>{
                        res.data.data.forEach(dataproduct => {
                            this.newProducts.push(dataproduct);
                        });
                        this.products.forEach(product => {
                            this.newProducts.push(product);
                        });
                        this.newProducts.forEach(newProduct =>{
                            this.filterProducts[newProduct.id] = newProduct;
                        });
                        this.filterProducts = this.filterProducts.filter(filterProduct => filterProduct.length != 0).reverse();
                        this.products = this.filterProducts;
                        this.newProducts = [];
                        this.filterProducts = [];
                    })
                    
                    this.trendings.forEach(trending =>{
                        if(trending.is_following == false && trending.id == id){
                            trending.is_following = true;
                        }
                    });

                    // this.errors(JSON.parse(res.data.data.alert));
                    this.$emit('alertNotification', this.errors);
                }else{
                }
            })
            .catch((err)=>console.log(err));
        },
        async unflwUser(id, name, page, event){
            await axios.post('/api/account/'+id+'/unfollow')
            .then((res)=>{
                console.log(res.data.data);
                if (res.data.data.status == 200) {
                    this.$emit('updateUser', {user: {
                        following : res.data.data.sender_count
                    }});
                    this.products = this.products.filter(product => product.user_id != id);

                    this.trendings.forEach(trending =>{
                        if(trending.is_following == true && trending.id == id){
                            trending.is_following = false;
                        }
                    });
                    this.$emit('alertNotification', this.errors);
                }else{

                }
            })
            .catch((err)=>console.log(err));
        },
        visitUser(event, vname){
            event.preventDefault();
            if ($(event.target).closest("button").length === 0) {
                this.$router.push({name: 'visit', params:{username: vname}, query:{v:'shop'}});
            }
        },
        /**
         * FOR INVENTORY PAGE
         */
        showSellModal(){
            $("#sell-modal").fadeIn();
        },
        triggerclick(){
            $(".product-sell-image").click();
        },
        async displayImage(e) {
            console.log(e);
            if (e.target.files[0]) {
                var reader = await new FileReader();
                reader.onload = function(e) {
                    var profileDisplay = $("#product-image-display");
                    for (var i = 0; i < profileDisplay.length; i++) {
                        $(profileDisplay[i]).attr("src", e.target.result);
                    }
                }
                reader.readAsDataURL(e.target.files[0]);
            }
        },
        showInventoryHelp(){
            $(".show-help, .show-help-col").fadeIn();
        },
        closeModal(e){
            if ($(e.target).closest(".vs-content").length === 0) {
                $(e.target).closest(".vs-wrapper").fadeToggle();
                $(".add-to-inventory")[0].reset();
            }
        },
        closeThisModal(e){
            $(e.target).closest(".vs-wrapper").fadeToggle();
            $(".add-to-inventory")[0].reset();
        },
        async addToInventory(e){
            $(".add-to-inventory button[type=submit]").html('<span class="loading-circle "></span>');
            if (this.user.products_lifetime <= 3 && this.user.payment_verified == 0) {
                var fd = new FormData(e.target);
                await axios.post('/api/shop', fd, {
                    headers:{
                        'Content-Type': 'multipart/form-data' 
                    }
                })
                .then((res)=>{
                    $(".add-to-inventory button[type=submit]").html('SAVE');
                    console.log(res.data.data);
                    this.products.shift(res.data.data.product);
                    this.$emit('update_products_lifetime', res.data.data.products_lifetime);
                    this.closeThisModal(e);
                    $(e.target).reset();
                })
                .catch(err=>{console.log(err)});
            }else{
            }
        },
        
    },
}
</script>
<style scoped>
#sell-modal{
    display: none;
}

.close-sell-modal{
    outline: none;
    position: absolute;
    top: 5px;
    right: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    border: none;
    background: none;
    font: inherit;
    color: var(--red-color);
    font-size: 1.8rem;
    font-weight: bold;
}
.sell-btn-div{
    position: fixed;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    bottom: 70px;
    left: 15px;
    z-index: 1;
}
.sell-btn-div button{
    display: inline-block;
    width: inherit;
    height: inherit;
    color: var(--brand-color);
    border-radius: 50%;
    background: var(--white-color);
    border: none;
    box-shadow: 0 0 4px 0 rgba(0, 0, 0, 0.15);
    font-size: 1.1rem;
}
.inventory-h{
    padding: .5rem;
    display:grid;
    grid-template-columns: 75% 25%; 
    justify-content: center;
    align-items:center;
}
#help{
    border: none;
    font-size: inherit;
    font-weight: normal;
    padding: .3rem;
    color: #fff;
    border-radius:1rem;
    background:limegreen;
    cursor: pointer;
    /* box-shadow: 0 0 4px 0 rgba(0,0,0,.5); */
}
.inventory-img{
    height: 330px;
}
.inventory-img img{
    height: 100%;
}
.show-help-col{
    display: none;
}
.show-help .close{
    float: right;
    font-size: 2rem;
    color: red;
    cursor: pointer;
}
.help-div .sb-text{
    width: 75%;
    margin: 0 auto;
}
.help-div p{
    line-height: 1.8rem;
    margin: .8rem 0;
    font-size: .9rem;
    padding: .2rem;
    text-indent: .5rem;
}
.help-div-2 span{
    border-radius: 5px;
}
.help-div .modal-views{
    border: none;
    width: fit-content;
    font-size: 1em;
    padding: .5em;
    background: linear-gradient(45deg, tomato, gold);
    /* box-shadow: 0 0 4px rgba(0, 0, 0, 0.5); */
    color: #fff;
}
.help-div .modal-btn-success{
    border-radius: 5px;
    border: none;
    width: fit-content;
    font-size: 1em;
    padding: .5em;
    /* box-shadow: 0 0 4px rgba(0, 0, 0, 0.5); */
    background: limegreen;
    color: #fff;
    font-weight: bold;
    text-transform: uppercase;
}
.help-div .modal-btn-delete{
    border: none;
    width: fit-content;
    font-size: 1em;
    padding: .5em;
    /* box-shadow: 0 0 4px rgba(0, 0, 0, 0.5); */
    background: rgba(239, 59, 34, .92);
    color: #fff;
    font-weight: bold;
    text-transform: uppercase;
}
.help-div-last{
    text-align: center;
    color: limegreen;
    font-family:cursive;
}
.help-div-last2{
    display: inline;
    width: 100%;
    /* float: right;
    text-align: center;
    margin-top: .5rem; */
}
.social-links a{
    color: #111;
}
.modal-product-content .modal-edit-product{
    padding-top: 1rem;
    width: 100%;
    border-top: 2px solid rgba(204, 204, 204, .7);
    display: grid;
    justify-content: flex-start;
    grid-template-columns: 1fr;
    grid-gap: .3rem;
}
.modal-product-content label{
    font-size: 1.05rem;
    border-radius: .3rem;
    padding:.3rem;
    font-weight:500;
    margin-right: .5rem;
}
.modal-edit-product .modal-editstatus{
    background: rgba(239, 59, 34, 1);
    color: #fff;
}
.modal-edit-statusVal{
    margin-left: .5rem;
    margin-right: .2rem;
}
.modal-edit-product input[type="number"]{
font-size: inherit;
width: 35%;
padding: .5rem;
border: none;
border-radius: .3rem;
line-height: 1.2rem;
border: 2px solid lightgrey;
}
.modal-edit-product textarea{
border: 2px solid lightgrey;
height: 70px;
width: 95%;
line-height: 1.2rem;
padding: .5rem;
border-radius: .3rem;
overflow: hidden;
}
.modal-edit-product-hide{
display: none;
}
/* #modal-footer{
display: grid;
justify-items: flex-end;
margin-top: .5em;
align-items: center;
position: relative;
} 

.modal-footer-btn{
position: relative;
}*/
.modal-footer{
    display:flex;
    justify-content: flex-end;
}
.modal-footer button{
    background: #fff;
    padding: .5em;
    border: 2px solid #ccc;
    border-radius:.3rem;
    cursor: pointer;
}
.modal-footer .modal-btn-delete{
    border: none;
    width: fit-content;
    font-size: 1em;
    padding: .5em;
    box-shadow: 0 0 4px rgba(0, 0, 0, 0.5);
    background: rgba(239, 59, 34, .92);
    color: #fff;
    font-weight: bold;
    text-transform: uppercase;
}
.modal-footer .modal-btn-success{
    border: none;
    width: fit-content;
    font-size: 1em;
    padding: .5em;
    box-shadow: 0 0 4px rgba(0, 0, 0, 0.5);
    background: limegreen;
    color: #fff;
    font-weight: bold;
    text-transform: uppercase;
}

</style>