<template>
    <div>
        <div class="wrapper">
            <div class='sb-text'>
                <h3 class=''><i class='fas fa-info-circle'></i> Account Info</h3>
            </div>
            <div class='seller-profile'>
                <div class='seller-img-col'>
                    <img loading="lazy" :src="asset('storage/profile_images/'+visited.image)" :alt="visited.name" @click="viewImage(asset('storage/profile_images/'+visited.image))" class="shop-seller-image">
                </div>
                <div class='seller-info-col'>
                    <p>
                        <label class='seller-info-label'><i class="fas fa-user"></i></label> <span style="font-weight:bold;word-break:break-all;">{{visited.name}}</span>
                    </p>
                    <p>
                        <label class='seller-info-label'><i class="fas fa-map-marker-alt"></i></label> <span>{{visited.geo_location}}</span>
                    </p>
                    <p>
                        <label class='seller-info-label'><i class="fas fa-info-circle"></i></label> <span>{{visited.bio}}</span>
                    </p>
                    <p class="_s-f">
                        <router-link :to="{name:'visit', params:{username: visited.name}, query:{v: 'following'}}">{{visited.following}} <span class="_s-f-c">Following</span></router-link>
                        
                        <router-link :to="{name:'visit', params:{username: visited.name}, query:{v:'follower'}}"><span id='v-followers'>{{visited.followers}} </span><span class="_s-f-c">Followers</span></router-link>
                    </p>
                    <p style="text-align:center;" :class="'btndiv_'+visited.id+' visited_influence'">
                        <button v-if="visited.is_following == true" type="button" name="button" class="seller-unfollow-btn" id="unflw_shp" @click="unflwUser(visited.id, visited.name, 'visited')"> Unfollow</button>
                        <button v-else-if="visited.is_following == false" type="button" name="button" class="seller-follow-btn" id="flw_shp" @click="flwUser(visited.id, visited.name, 'visited')"> Follow</button>
                    </p>
                </div>
            </div>
            <div class='visit-action-div' id="visitAction">
                <router-link :to="{name: 'visit', params:{username: $route.params.username}, query: {v: 'shop'}}" :class="$route.query.v == 'shop'? 'seller-product-header shop-action':'visit-default-btn'" style="text-decoration:none;">
                    <i class="fas fa-shopping-bag"></i> Products
                </router-link>
                <router-link :to="{name: 'visit', params:{username: $route.params.username}, query: {v: 'broadcast'}}" :class="$route.query.v == 'broadcast'? 'seller-product-header broadcast-action':'visit-default-btn'" style="text-decoration:none;">
                    <i class="fas fa-bullhorn"></i> Broadcasts
                </router-link>
            </div>
        </div>
        
        <div v-if="['shop', 'broadcast',].includes($route.query.v)">
            <div v-if="$route.query.v == 'shop'" class="_idb">
                <div v-if="products.length > 0">
                    <products-component v-bind:products="products" v-bind:user="user" v-on:viewImage="viewImage"></products-component>
                    <infinite-loading @infinite="infiniteHandler"></infinite-loading>
                </div>
                <div v-else class="wrapper">
                    <div  class="idb">
                        <div class="shop-empty-col">
                            <img loading="lazy" :src="asset('assets/illustrations/lantern_animate.svg')" :alt="'Empty-Cart'">
                            <div>
                                <p>Oops! {{visited.name}} has no products yet</p>Probably a boring guy
                                <br><br>
                                <router-link :to="{name: 'shop', params:{page:'buy'}}"><i class="fas fa-search"></i> Find exciting Shops</router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="wrapper">
                <broadcast-component v-bind:broadcasts="broadcasts" v-bind:user="user"  v-on:viewImage="viewImage" v-on:appendBroadcast="appendBroadcast" v-on:updateComment="updateComment" v-on:showCommentBox="showCommentBox" v-on:showOptionBox="showOptionBox" v-on:showShareBox="showShareBox" v-bind:visited="visited"></broadcast-component>
                <infinite-loading @infinite="infiniteHandler"></infinite-loading>

                <events-component v-bind:user="user" v-on:updateComment="updateComment" v-bind:showComment="showComment" v-bind:showOption="showOption" v-bind:createBroadcast="createBroadcast" v-on:closeBroadcast="closeBroadcast" v-bind:showShare="showShare" v-bind:tweet="tweet" v-on:closeComment="closeComment" v-on:closeOption="closeOption" v-on:closeShare="closeShare" v-on:viewImage="viewImage" v-on:updateBroadcasts="updateBroadcasts" v-on:addedBookmark="addedBookmark" v-on:removedBookmark="removedBookmark" v-on:updateUser="updateUser"></events-component>   
            </div>
        </div>
        <div v-else-if="['follower', 'following'].includes($route.query.v)" >
            <div class="wrapper">
                <div v-if="accounts.length > 0">
                    <h3 style="color:var(--brand-color);padding:1rem;text-align:center;white-space:normal;">
                        <span v-if="$route.query.v == 'following'"> Following </span>
                        <span v-else> Followers </span>
                    </h3>
                    <div v-for="account in accounts" v-bind:key="account.id+'-'+account.name" class="acct-col acct-div">
                        <div class="acct-img-div">
                            <router-link :to="{name: 'visit', params:{username: account.name}, query:{v:'shop'}}"><img loading="lazy" class="acct-img" :src="asset('storage/profile_images/'+account.image)" :alt="account.name"/></router-link>
                        </div>
                        <div class="acct-info">
                            <div style="display:grid; grid-template-columns: repeat(2, 1fr);">
                                <p style="font-weight:bold;word-break:break-all;">{{account.name}}</p>
                                <div class="btn-col btndiv">
                                    <button v-if="account.is_following == true" class="seller-unfollow-btn btnid_" @click="unflwUser(account.id, account.name, 'accounts')">Unfollow</button>
                                    <button v-else-if="account.is_following == false" class="seller-follow-btn btnid_" @click="flwUser(account.id, account.name, 'accounts')">Follow</button>
                                </div>
                            </div>
                            <div style="position: relative;">
                                <span v-if="account.follows_you" class="check-following"><i class="fas fa-user"></i> Follows you</span>
                            </div>
                        </div>
                    </div>
                    <infinite-loading @infinite="infiniteHandler"></infinite-loading>
                </div>
                <div v-else class="idb">
                    <div class="shop-empty-col">
                        <img loading="lazy" :src="asset('assets/illustrations/example-animate.svg')" alt="">
                        <div v-if="$route.query.v == 'follower'">
                            <p>Oops! {{visited.name != user.name? visited.name+" has" : 'You have'}}  no followers yet</p>Probably a boring guy
                            <br><br>
                            <router-link :to="{name: 'shop', params:{page:'buy'}}"><i class="fas fa-search"></i> Find exciting Shops</router-link>
                        </div>
                        <div v-else>
                            <p>Hmmm! {{visited.name != user.name? visited.name+" is" : 'You are'}}  following nobody yet</p>Probably a boring guy
                            <br><br>
                            <router-link :to="{name: 'shop', params:{page:'buy'}}"><i class="fas fa-search"></i> Find exciting Shops</router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
// import AccountComponent from '../components/AccountComponent';
// import FamousComponent from '../components/FamousComponent';
import ProductsComponent from '../components/shop/inc/ProductsComponent';
import BroadcastComponent from '../components/broadcast/inc/BroadcastComponent';
import InfiniteLoading from 'vue-infinite-loading';
import EventsComponent from '../components/broadcast/inc/EventsComponent';

export default {
    data() {
        return {
            visited: {
                name: '',
            },
            form:{
                visitedid: '',
                action: ''
            },
            products:[],
            accounts:[],
            broadcasts:[],
            
            tweet:{},
            showComment: false,
            showShare: false,
            showOption: false,
            type: '',
            api: '',
            page: 1,
            visited_gotten: false,
        }
    },
    props: ["user", "createBroadcast"],
    components:{
        ProductsComponent,
        BroadcastComponent,
        EventsComponent,
        InfiniteLoading
    },
    watch: {
        async $route(){
            this.page = 1
            this.products = [];
            this.broadcasts = [];
            this.accounts = [];
            this.visited_gotten = false;
            await axios.get('/api/account/'+this.$route.params.username)
            .then((res)=>{
                this.visited = res.data.data.visited;
                this.visited_gotten = true;
            })
            .catch(err=>{console.log(err)});
        },
        visited_gotten: async function (data) {
            console.log(data);
            if (data == true) {
                if (this.$route.query.v == 'shop') {
                    this.api = await '/api/shop/'+this.visited.id;
                }else if(['follower', 'following'].includes(this.$route.query.v)){
                    this.api = await '/api/account/'+this.visited.id+'/'+this.$route.query.v; 
                }else if(this.$route.query.v == 'broadcast'){
                    this.api = await '/api/broadcast/'+this.visited.id;
                }
                await this.infiniteHandler();
                // this.visited_gotten = false;
            }
        }
    },
    async created() {
        await axios.get('/api/account/'+this.$route.params.username)
        .then((res)=>{
            this.visited = res.data.data.visited;
            this.visited_gotten = true;
            })
        .catch(err=>{console.log(err)});
        if (this.$route.query.pid != '') {
            this.form.productid = this.$route.query.pid;
            // this.form.action = 'new-visit';
            // axios.post('/api/action/'+this.$route.query.pid+'/product-impression/visit')
            // .then((res)=>{
            // })
            // .catch(err=>console.log(err));
        }

    },
    methods: {
        viewImage(src){
            this.$emit('viewImage', src);
        },
        async infiniteHandler($state) {
            await axios.get(this.api, {
                params: {
                page: this.page,
                },
            }).then((res) => {
                console.log(res.data.data);
                if (res.data.data.length) {
                    this.page += 1;
                    if (this.$route.query.v == 'shop') {
                        this.products.push(...res.data.data);
                    }else if(['follower', 'following'].includes(this.$route.query.v)){
                        this.accounts.push(...res.data.data);
                    }else if(this.$route.query.v == 'broadcast'){
                        res.data.data.forEach((broadcast) =>{
                            if(broadcast.media != null){
                                broadcast.media = JSON.parse(broadcast.media);
                            }
                            if(broadcast.is_comment == true){
                                if(broadcast.original_post.media != null){
                                    broadcast.original_post.media = JSON.parse(broadcast.original_post.media);
                                }
                            }
                            
                        })    
                        this.broadcasts.push(...res.data.data);
                    }
                    $state.loaded();
                } else {
                    $state.complete();
                }
            })
            .catch(err=>console.log(err));
        },
        async flwUser(id, name, type){
            await axios.post('/api/account/'+id+'/follow')
            .then((res)=>{
                if (res.data.data.status == 200) {
                    this.$emit('updateUser', {user: {
                        following : res.data.data.sender_count
                    }});
                    if (type == 'accounts') {
                        this.accounts.forEach((account)=>{
                            if (account.id == id) {
                                account.is_following = true;
                            }
                        });
                    }else{
                        this.visited.is_following = true;
                        this.visited.followers = res.data.data.receiver_count;
                    }
                    this.$emit('alertNotification', this.errors);
                    if(this.$route.params.username == this.user.name){
                        this.visited.following = res.data.data.sender_count;
                    }
                }
            })
            .catch((err)=>console.log(err));
        },

        async unflwUser(id, name, type){
            await axios.post('/api/account/'+id+'/unfollow')
            .then((res)=>{
                console.log(res.data.data);
                if (res.data.data.status == 200) {
                    this.$emit('updateUser', {user: {
                        following : res.data.data.sender_count
                    }});
                
                    if (type == 'accounts') {
                        this.accounts.forEach((account)=>{
                            if (account.id == id) {
                                account.is_following = false;
                            }
                        });
                    }else{
                        this.visited.is_following = false;
                        this.visited.followers = res.data.data.receiver_count;
                    }
                    if(this.$route.params.username == this.user.name){
                        if(this.$route.query.v == 'following'){
                            this.accounts = this.accounts.filter(account => account.id != id);
                        }
                        this.visited.following = res.data.data.sender_count;
                    }
                    this.$emit('alertNotification', this.errors);
                }
            })
            .catch((err)=>console.log(err));
        },

        closeBroadcast(){
            this.$emit('closeBroadcast');
        },
        updateUser(user){
            if (this.$route.params.username == this.user.name) {
                console.log(user);
                this.visited.following = user.user.following;
            }
            this.$emit('updateUser', user);
        },
        async appendBroadcast(broadcast){
            if (broadcast.media != null) {
                broadcast.media = await JSON.parse(broadcast.media);
            }
            await this.broadcasts.unshift(broadcast);
        },
        async updateComment(){
            await this.broadcasts.forEach((element)=>{
                if (this.type == 'origin') {
                    if (element.type == 'comment') {
                        if (element.original_post.id == this.tweet.id) {
                            return element.original_post.comments += 1;
                        }
                    }
                }
                if (element.id == this.tweet.id) {
                    return element.comments += 1;
                }
            });
        },
        showOptionBox(data){
            this.tweet = data.tweet;
            this.type = data.type;
            this.showOption = true;
        },
        showCommentBox(data){
            this.tweet = data.tweet;
            this.type = data.type;
            this.showComment = true;
        },
        showShareBox(data){
            this.tweet = data.tweet;
            this.type = data.type;
            this.showShare = true;
        },
        closeComment(){
            this.showComment = false;
        },
        closeShare(){
            this.showShare = false;
        },
        closeOption(){
            this.showOption = false;
        },
        async addedBookmark(){
           await this.broadcasts.forEach((element)=>{
                if (this.type == 'origin') {
                    if (element.type == 'comment') {
                        if (element.original_post.id == this.tweet.id) {
                            return element.original_post.bookmarked = true;
                        }
                    }
                }
                if (element.id == this.tweet.id) {
                    return element.bookmarked = true;
                }
            });
            this.closeShare();
        },
        async removedBookmark(){
           await this.broadcasts.forEach((element)=>{
                if (this.type == 'origin') {
                    if (element.type == 'comment') {
                        if (element.original_post.id == this.tweet.id) {
                            return element.original_post.bookmarked = false;
                        }
                    }
                }
                if (element.id == this.tweet.id) {
                    return element.bookmarked = false;
                }
            });
            this.closeShare();
        },
        async updateBroadcasts(id){
            if(this.$route.params.page == undefined){
                this.broadcasts = this.broadcasts.filter(broadcast => broadcast.retweeter_id != id);
                await this.broadcasts.forEach((broadcast, index) =>{
                    if(broadcast.type == "comment"){
                        if (broadcast.original_post.user_id == id) {
                            broadcast.original_post.is_following = false;
                        }
                    }
                    if (broadcast.user_id == id) {
                        broadcast.is_following = false;
                    }
                    console.log(broadcast.retweeter_id);
                    if(broadcast.retweeter_id == undefined && broadcast.user_id == id){
                        this.broadcasts.splice(index, 1);
                    }
                });
            }
            // this.showOption = false;
            this.closeOption();
        },
        
    }
}
</script>

<style type="text/css">
    .acct-col:nth-child(even) {
        background: var(--bg-color);
    }

    .acct-col {
        text-decoration: none;
        color: var(--dark-color);
        border-radius: 1rem;
        padding: .3rem;
        display: flex;
        align-items: center;
        -webkit-border-radius: 1rem;
        -moz-border-radius: 1rem;
        -ms-border-radius: 1rem;
        -o-border-radius: 1rem;
    }

    .acct-col .acct-img-div {
        display: grid;
        justify-items: left;
    }

    .acct-col .acct-img {
        margin-right: .5rem;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
        object-fit: cover;
    }

    .acct-info {
        width: 100%;
    }

    .btn-col {
        width: 100%;
        text-align: right;
    }

    .btn-col button {
        cursor: pointer;
        width: 75%;
        font-size: 1em;
        padding: .3rem;
        font-weight: bold;
        border-radius: 1rem;
        text-transform: capitalize;
        border: 2px solid var(--brand-color);
    }

    .acct-bio {
        width: 100%;
        display: inline-block;
        height: 10px;
    }

    .check-following {
        color: var(--brand-color);
        font-size: .8rem;
        position: absolute;
        bottom: -10px;
        left: 10px;
    }
.close-shop {
    color: var(--white-color);
    background: var(--red-color);
    padding: .5rem;
    border-radius: .3rem;
    font-weight: bold;
    cursor: pointer;
    opacity: .8;
    z-index: 1000;
    border: none;
    box-shadow: 0 0 4px rgba(100, 100, 100, .75);
    font-size: 1.05rem;
}

.seller-profile {
    margin-top: .5rem;
    padding: .3rem;
    display: grid;
    background: var(--bg-color);
    grid-gap: .2em;
    border-radius: 1rem;
    -webkit-border-radius: 1rem;
    -moz-border-radius: 1rem;
    -ms-border-radius: 1rem;
    -o-border-radius: 1rem;
}

@media screen and (min-width:768px) {
    .seller-profile {
        grid-template-columns: 40% 60%;
    }
}

.seller-img-col {
    text-align: center;
    justify-self: center;
    align-self: center;
}

.seller-info-col {
    padding: .3rem;
}

.seller-info-col p {
    margin-bottom: .6em;
}

.seller-info-col ._s-f {
    display: flex;
    justify-content: center;
    font-weight: bold;
    color: var(--dark-color);
    border-top: 1px solid rgba(128, 128, 128, .7);
    text-align: center;
}

._s-f a {
    text-decoration: none;
    color: #444;
}

._s-f-c {
    margin-right: .5rem;
    color: var(--dark-color);
    font-weight: normal;
}

.shop-seller-image {
    height: 100px;
    width: 100px;
    border-radius: 50%;
    object-fit: cover;
    position: relative;
}

.seller-info-label {
    text-transform: capitalize;
    border-radius: 5px;
    padding: .3em;
    color: var(--white-color);
    background: var(--brand-color);
}

.seller-rating-col {
    text-align: center;
    grid-column: 1/5;
}

.visited_influence button {
    cursor: pointer;
    width: 75%;
    font-size: 1em;
    padding: .5em;
    font-weight: bold;
    border-radius: 1rem;
    text-transform: capitalize;
    border: 2px solid var(--brand-color);
}

.seller-product-header {
    background: var(--white-color);
    font-weight: bold;
    text-align: center;
    padding: .5em;
    font-size: 1rem;
    box-shadow: none;
    color: var(--highlight-color);
    border-bottom: 2px solid var(--highlight-color);
}

.visit-action-div {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    background: var(--white-color);
    padding: .5rem;
}

.visit-default-btn {
    padding: .5rem;
    font-weight: bold;
    text-align: center;
    color: var(--dark-color);
    background: var(--white-color);
}

.visit-action-div a {
    text-decoration: none;
    border-radius: .5rem;
    border: none;
    border-radius: 0;
}

.visit-action-div a:hover {
    color: var(--highlight-color);
}
</style>