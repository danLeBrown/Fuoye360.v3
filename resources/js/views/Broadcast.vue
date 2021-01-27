<template>
    <div>
        <div v-if="$route.params.page == 'status'">
            <thread-component  v-on:updateUser="updateUser" v-bind:createBroadcast="createBroadcast" v-on:viewImage="viewImage" v-on:closeBroadcast="closeBroadcast" v-bind:user="user"></thread-component>
        </div>
        <div v-else>
            <div class="wrapper">
                <a href="#" v-if="$route.params.page == undefined" class="new-tweets-available" @click="pushBroadcast"><i class="fas fa-bell"></i> NEW BROADCASTS</a>
                <!-- <div  :style="$route.params.page == undefined? 'display:none;': ''">
                </div> -->
                <div v-else-if="$route.params.page == 'trending'" >
                    <div class='sb-text'>
                        <h3 class=''><i class='fas fa-fire'></i> Trending</h3>
                    </div>
                </div>
                <div v-else-if="$route.params.page == 'bookmarks'" >
                    <div class='sb-text'>                
                        <h3 class=''><i class='fas fa-bookmark'></i> Bookmarks</h3>
                    </div>
                </div>
                <div v-if="broadcasts.length > 0 || createBroadcast == true">
                    <div class="display-tweets" id="display-tweets">
                        <broadcast-component v-bind:broadcasts="broadcasts" v-bind:user="user" v-on:viewImage="viewImage" v-on:updateComment="updateComment" v-on:showCommentBox="showCommentBox" v-on:showOptionBox="showOptionBox" v-on:showShareBox="showShareBox"></broadcast-component>
                        <infinite-loading @infinite="infiniteHandler"></infinite-loading>
                    </div>
                    <events-component v-bind:user="user" v-bind:updateUser="updateUser" v-on:updateComment="updateComment" v-bind:showComment="showComment" v-bind:showOption="showOption" v-bind:createBroadcast="createBroadcast" v-bind:showShare="showShare" v-bind:tweet="tweet" v-on:closeComment="closeComment" v-on:closeOption="closeOption" v-on:closeShare="closeShare" v-on:viewImage="viewImage" v-on:closeBroadcast="closeBroadcast" v-on:updateBroadcasts="updateBroadcasts" v-on:addedBookmark="addedBookmark" v-on:removedBookmark="removedBookmark" v-on:createdThread="createdThread" v-on:appendBroadcast="appendBroadcast"></events-component>   
                </div>
                <div v-else class="shop-empty-col">
                    <img :src="asset('assets/illustrations/broadcast-animate.svg')" :alt="'Empty-Bookmark'">
                    <div v-if="$route.params.page == undefined">
                        <p>Oops! No broadcasts at this time</p>
                        <a href="#" class="blog-broadcast" @click.prevent="newBroadcast"><i class="fas fa-bullhorn"></i> Broadcast</a>
                        <div>
                            OR
                        </div>
                    </div>
                    <div v-else-if="$route.params.page == 'bookmarks'">
                        <p>Oops! No bookmarked Broadcasts at the moment</p>
                    </div>
                    <div v-else-if="$route.params.page == 'trending'">
                        <p>Oops! No trending Broadcasts at the moment</p>
                    </div>
                    <router-link :to="{name: 'search'}"><i class="fas fa-search"></i> Find exciting broadcasts elsewhere</router-link>
                </div>    
            </div>
        </div>
        <!-- <index-component  v-bind:user="user" v-on:updateUser="updateUser" v-on:viewImage="viewImage" v-bind:createBroadcast="createBroadcast" v-on:newBroadcast="newBroadcast" v-on:closeBroadcast="closeBroadcast"></index-component>
        <trending-component v-bind:user="user" v-bind:createBroadcast="createBroadcast" v-on:updateUser="updateUser"  v-on:viewImage="viewImage" v-on:closeBroadcast="closeBroadcast"></trending-component>
        <bookmark-component v-if="$route.params.page == 'bookmarks'" v-bind:user="user" v-on:updateUser="updateUser" v-bind:createBroadcast="createBroadcast" v-on:viewImage="viewImage" v-on:closeBroadcast="closeBroadcast"></bookmark-component> -->
    </div>
</template>
<script>
// import IndexComponent from '../components/broadcast/IndexComponent'
// import TrendingComponent from '../components/broadcast/TrendingComponent'
// import BookmarkComponent from '../components/broadcast/BookmarkComponent'
import InfiniteLoading from 'vue-infinite-loading';
import ThreadComponent from '../components/broadcast/ThreadComponent'
import BroadcastComponent from '../components/broadcast/inc/BroadcastComponent'
import EventsComponent from '../components/broadcast/inc/EventsComponent'
export default {
    name: 'broadcast',
    data() {
        return {
            broadcasts: [],
            tweet:{},
            showComment: false,
            showShare: false,
            showOption: false,
            type: '',
            createdThreadStatus: '',
            api: '',
            page: 1,
            push_broadcasts: [],
            broadcast_pushed: false
        }
    },
    props: ["user", "createBroadcast"],
    components:{
        InfiniteLoading,
        ThreadComponent,
        BroadcastComponent,
        EventsComponent
    },
    watch:{
        $route(){
            this.page = 1;
            this.broadcasts = [];
            this.createdThreadStatus= '';
            this.setUpBroadcastPage();
        },
        push_broadcasts : function () {
            setTimeout(() => {
                if (this.broadcast_pushed === false) {
                    this.push_broadcasts.forEach(broadcast =>{
                        this.broadcasts.unshift(broadcast);
                        $(".new-tweets-available").animate({
                            top: -100
                        });
                        this.broadcast_pushed = true;
                        this.push_broadcasts = [];
                    });
                }
            }, 3000);
        }
    },
    mounted() {
        if(this.$route.params.page == undefined){
          window.Echo.bind('NewNotification', (e)=>{
                e.PUSH_NOTIFICATION_RECEIVERS.forEach(id =>{
                    console.log(e);
                    if (id == this.user.id) {
                        $(".new-tweets-available").fadeIn();
                        e.PUSH_BROADCASTS.forEach(broadcast =>{
                            if(broadcast.media != null){
                                broadcast.media = JSON.parse(broadcast.media);
                            }
                            if(broadcast.is_comment == true){
                                if(broadcast.original_post.media != null){
                                    broadcast.original_post.media = JSON.parse(broadcast.original_post.media);
                                }
                            }
                            if (this.push_broadcasts.length <= 0) {
                                this.push_broadcasts.push(broadcast);
                            }else{
                                this.push_broadcasts.unshift(broadcast);
                            }
                        });
                        $(".new-tweets-available").animate({
                            top: 100
                        });
                        this.broadcast_pushed = false;
                    }
                });
            });
        }
    },
    created() {
        this.setUpBroadcastPage();
    },
    methods: {
        async pushBroadcast(){
            if (this.broadcast_pushed != true) {
                this.push_broadcasts.forEach(broadcast =>{
                    this.broadcasts.unshift(broadcast);
                });
                $(".new-tweets-available").animate({
                    top: -100
                });
                this.broadcast_pushed = await true;
                this.push_broadcasts = [];
            }
        },
        async setUpBroadcastPage(){
            const current_page = this.$route.params.page;
            if(current_page == undefined){
                this.api = '/api/broadcast';
            }else if (current_page == "bookmark") {
                this.api = '/api/broadcast/bookmarks';
            }else if (current_page == "trending") {
                this.api = '/api/broadcast/trending';
            }
            await this.infiniteHandler();
        },
        async appendBroadcast(broadcast){
            if (this.$route.params.page == undefined) {
                if (broadcast.media != null) {
                    broadcast.media = await JSON.parse(broadcast.media);
                }
                await this.broadcasts.unshift(broadcast);            
            }
        },
        updateUser(data){
            this.$emit('updateUser', data);
        },
        newBroadcast(){
            this.$emit('newBroadcast');
        },
        closeBroadcast(){
            this.$emit('closeBroadcast');
        },
        viewImage(src){
            this.$emit('viewImage', src);
        },
        createdThread(){
            this.createdThreadStatus = true;
        },
        async updateBroadcasts(id){
            if(this.$route.name == 'broadcast' && this.$route.params.page == undefined){
                this.broadcasts = await this.broadcasts.filter(broadcast => broadcast.retweeter.id != id);
                await this.broadcasts.forEach((broadcast, index) =>{
                    if(broadcast.type == "comment"){
                        if (broadcast.original_post.user_id == id) {
                            broadcast.original_post.is_following = false;
                        }
                    }
                    if (broadcast.user_id == id) {
                        broadcast.is_following = false;
                    }
                    console.log(broadcast.retweeter.id);
                    if(broadcast.retweeter){
                        if (broadcast.retweeter.id == id && broadcast.retweets_count == 1) {
                            this.broadcasts.splice(index, 1);
                        }else if(broadcast.user_id == id && broadcast.retweeter.id == broadcast.user_id){
                            this.broadcasts.splice(index, 1);
                        }
                    }else{
                        if (broadcast.user_id == id) {
                            this.broadcasts.splice(index, 1);
                        }
                    }
                });
            }
            this.closeOption();
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
                    element.comments += 1;
                    if (this.createdThreadStatus) {
                        element.is_thread = true;
                    }
                }else{
                    if (element.type == 'comment') {
                        if (element.original_post.id == this.tweet.id) {
                            element.comments += 1;
                        }
                    }
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
            if (this.$route.params.page == 'bookmarks') {
                this.broadcasts = await this.broadcasts.filter(broadcast => broadcast.id != this.tweet.id);
            }else{
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
            } 
            this.closeShare();
        },
        async updateUser(user){
            if (this.$route.params.username == this.user.name) {
                console.log(user);
                this.visited.following = await user.user.following;
            }
            this.$emit('updateUser', user);
        },

        /**
         * 
         * 
         * 
         */
        async infiniteHandler($state) {
            await axios.get(this.api, {
                params: {
                page: this.page,
                },
            }).then((res) => {
                console.log(res);
                if (res.data.data.length) {
                    this.page += 1;
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
                    
                    $state.loaded();
                } else {
                    $state.complete();
                }
            })
            .catch(err=>console.log(err));
        },
        
    },
}
</script>
<style scoped>
.new-tweets-available{
    position: fixed;
    z-index: 5;
    margin: 0 auto;
    left: 0;
    right: 0;
    width: max-content;
    display: block;
    top: -100%;
    background: var(--brand-color);
    padding: .5rem;
    color: var(--white-color);
    border-radius: 1rem;
    box-shadow: 0 4px 4px 0 rgba(50, 205, 50, .25);
    text-decoration: none;
    font-size: .75rem;
    transition: all alternate-reverse 1500ms;
}
</style>