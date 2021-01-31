<template>
    <div>
        <div class="wrapper search-wrapper">
            <form class="search-query-div">
                <div>
                    <i class="fas fa-filter"></i>FILTER <select name="" id="" @change="changeSearchParam($event)">
                        <option value="users">Users</option>
                        <option value="shop">Shop</option>
                        <option value="broadcast">Broadcast</option>
                    </select>
                </div>
            </form>
            <div v-if="searchData != ''">
                <div v-if="type == 'users'">
                    <div v-if="accounts.length > 0">
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
                    </div>
                    <div v-else class="idb">
                        <div class="shop-empty-col">
                            <img loading="lazy" :src="asset('assets/illustrations/example-animate.svg')" alt="">
                            <p>Oops! No User Found</p>
                            Perhaps try entering another search or changing the filter
                            <router-link :to="{name: 'shop', params:{page:'buy'}}"><i class="fas fa-search"></i> Find exciting Shops</router-link>
                        </div>
                    </div>
                </div>
                <div v-else-if="type =='broadcast'">
                    <div v-if="broadcasts.length > 0">
                        <div class="display-tweets" id="display-tweets">
                            <broadcast-component v-bind:broadcasts="broadcasts" v-bind:user="user" v-on:viewImage="viewImage" v-on:updateComment="updateComment" v-on:showCommentBox="showCommentBox" v-on:showOptionBox="showOptionBox" v-on:showShareBox="showShareBox"></broadcast-component>
                        </div>
                        <events-component v-bind:user="user" v-bind:updateUser="updateUser" v-on:updateComment="updateComment" v-bind:showComment="showComment" v-bind:showOption="showOption" v-bind:createBroadcast="createBroadcast" v-bind:showShare="showShare" v-bind:tweet="tweet" v-on:closeComment="closeComment" v-on:closeOption="closeOption" v-on:closeShare="closeShare" v-on:viewImage="viewImage" v-on:closeBroadcast="closeBroadcast" v-on:updateBroadcasts="updateBroadcasts" v-on:addedBookmark="addedBookmark" v-on:removedBookmark="removedBookmark" v-on:createdThread="createdThread" v-on:appendBroadcast="appendBroadcast"></events-component>   
                    </div>
                    <div v-else class="shop-empty-col">
                        <img :src="asset('assets/illustrations/broadcast-animate.svg')" :alt="'Empty-Bookmark'">
                        <div v-if="$route.params.page == undefined">
                            <p>Oops! No broadcast found.</p>
                            Perhaps try entering another search or changing the filter
                        </div>
                        <router-link :to="{name: 'search'}"><i class="fas fa-search"></i> Find exciting broadcasts elsewhere</router-link>
                    </div>
                </div>
                <div v-else-if="type == 'shop'">
                    <div v-if="products.length <= 0">
                        <div class="shop-empty-col">
                            <img loading="lazy" :src="asset('assets/illustrations/example-animate.svg')" alt="">
                            <div>
                                <p>Oops! No product match.</p>
                                Perhaps try entering another search or changing the filter
                                <router-link :to="{name: 'shop', params:{page:'buy'}}"><i class="fas fa-search"></i> Find exciting Shops</router-link>
                            </div>
                        </div>
                    </div>
                    <div v-if="products.length && products.length > 0">
                        <products-component v-bind:products="products" v-bind:user="user" v-on:updateWishlist="updateWishlist" v-on:confirmDelete="confirmDelete"></products-component>
                    </div>
                </div>
                <infinite-loading v-if="products.length > 0 || accounts.length > 0 || broadcasts.length > 0 " @infinite="infiniteHandler"></infinite-loading>
            </div>
            <div v-else>
                <div class="shop-empty-col">
                    <img loading="lazy" :src="asset('assets/illustrations/easter-egg-hunt-animate.svg')" alt="">
                    <div>
                        <p>Start searching. Stop Playing.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>
    form{
        padding: 0;
        /* margin: 0 1rem; */
    }
    .search-div{
        background: var(--white-color);
    }
    .search-div input{
        flex: 9;
        padding: .5rem !important;
    }
    .search-div button{
        flex: 3;
    }
    .search-wrapper{
        min-height: calc(100vh - 110px);
    }
    .search-query-div{
        display: flex;
        justify-content: flex-end;
    }
    .search-query{
        padding: .5rem;
    }
    .search-query-div{
        font: inherit;
        color: var(--brand-color);
        font-size: .75rem;
        font-weight: bold;
    }
    .search-query-div select{
        padding: .25rem;
        border-radius: .5rem;
        border: 1px solid var(--brand-color);
        color: var(--brand-color);
    }
</style>
<script>
import ProductsComponent from '../components/shop/inc/ProductsComponent';
import BroadcastComponent from '../components/broadcast/inc/BroadcastComponent';
import InfiniteLoading from 'vue-infinite-loading';
import EventsComponent from '../components/broadcast/inc/EventsComponent';
export default {
    data() {
        return {
            api: '',
            accounts: [],
            broadcasts: [],
            products: [],
            page: 1,
            type: ''
        }
    },
    components:{
        BroadcastComponent,
        EventsComponent,
        ProductsComponent,
        InfiniteLoading
    },
    props: ["searchData", "user"],
    watch:{
        searchData: async function () {
            this.page = 1;
            this.accounts = [];
            this.products = [];
            this.broadcasts = [];
            this.type = this.type == ''? 'users': this.type;
            this.api = await '/api/search/'+this.searchData+'/'+this.type;
            await this.infiniteHandler();
            // if (this.$route.query.query == undefined) {
            // }
        }
    },
    methods:{
        async infiniteHandler($state) {
            await axios.get(this.api, {
                params: {
                    page: this.page,
                },
            }).then((res) => {
                console.log(res.data.data);
                if (res.data.data.length) {
                    this.page += 1;
                    if (this.type == 'shop') {
                        this.products.push(...res.data.data);
                    }else if(this.type == 'users'){
                        this.accounts.push(...res.data.data);
                    }else if(this.type == "broadcast"){
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
                this.$emit('changeSearchState');
            })
            .catch(err=>console.log(err));
        },
        changeSearchParam(e){
            console.log(e);
            this.type = e.target.value;
            this.api = '/api/search/'+this.searchData+'/'+e.target.value;
            this.page = 1;
            this.accounts = [];
            this.products = [];
            this.broadcasts = [];
            this.$emit('searchQuery', {query: this.searchData})
            this.infiniteHandler();
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