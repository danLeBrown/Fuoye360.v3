<template>
    <div>
        <div class="wrapper">
            <div class="sb-text">
                <span v-if="$route.query.t == 'thread'"><i class="fas fa-newspaper"></i> Thread</span>
                <span v-else-if="$route.query.t == 'comment'"><i class="fas fa-comment"></i> Comment</span>
                <span v-else-if="$route.query.t == 'broadcast'"><i class="fas fa-bullhorn"></i> Broadcast</span>
                
            </div>
            <div v-if="broadcasts.length > 0">
                <div class="display-tweets">
                    <div v-for="broadcast in broadcasts" v-bind:key="broadcast.id" :data-href="'broadcast/status/'+broadcast.id" class="view-thread tweet-div" :data-status="broadcast.id" style="border:none;" :style="$route.params.status != broadcast.id ? 'transform: scale(.9)' : ''">
                        <div class="tweet-content-div for-thread" @click="viewStatus(broadcast.id, broadcast.type, $event )">
                            <div v-if="to_hide_border != broadcast.id" class="thread-border"><span class="border-content"></span></div>
                            <div class="tweet-img-div">
                                <router-link :to="{name: 'visit', params:{username: broadcast.user.name}, query:{v:'broadcast'}}"><img loading="lazy" :src="asset('storage/profile_images/'+broadcast.user.image)" :alt="broadcast.user.name" class="tweeter-img"></router-link>
                            </div>
                            <div class="tweet-txt-div">
                                <div class="tweet-profile-div">
                                    <button class="tweet-options" @click="showOptionBox(broadcast, 'origin')"><i class="fas fa-angle-down"></i></button>
                                    <span class="tweet-username">{{broadcast.user.name}}</span><span class="tweet-time">. {{broadcast.timeago}}
                                    </span>
                                </div>
                                <div v-html="broadcast.body" class="tweet-body" style="text-decoration: none;color:#000;">
                                </div>
                                <div class="broadcast-media" v-if="broadcast.media != null">
                                    <img loading="lazy" :src="asset('storage/broadcast_images/'+img)" alt="" v-for="img in broadcast.media" v-bind:key="img.id" load="lazy" @click="viewImage(asset('storage/broadcast_images/'+img))">
                                </div>
                                <div class="tweet-func-div">
                                    <button class="" @click="showCommentBox(broadcast, 'origin')"><i class="far fa-comment"></i> <span class="comment-count">{{broadcast.comments}}</span></button>
                                    <button v-if="broadcast.is_rebroadcast" class="btn_retweet_broadcast.id}} tweet-retweet" @click="undoRebroadcast(broadcast.id, 'origin')"><i class="fa fa-bullhorn"></i> {{broadcast.rebroadcasts}}</button>
                                    <button v-else class="btn_retweet_broadcast.id}}" @click="rebroadcast(broadcast.id, 'origin')"><i class="fa fa-bullhorn"></i> {{broadcast.rebroadcasts}}</button>
                                    <button v-if="broadcast.is_liked" class="btn_like_broadcast.id}} tweet-liked" @click="unlikeBroadcast(broadcast.id, 'origin')"><i class="fas fa-heart"></i> {{broadcast.likes}}</button>
                                    <button v-else class="btn_like_broadcast.id}}" @click="likeBroadcast(broadcast.id, 'origin')"><i class="far fa-heart"></i> {{broadcast.likes}}</button>                        
                                    <button @click="showShareBox(broadcast, 'origin')"><i class="fas fa-share-alt"></i></button>
                                </div>    
                                <span v-if="broadcast.is_thread" style="background:var(--brand-color);padding:.25rem .5rem;color:var(--white-color);border-radius:1rem;font-size:.75rem;"><i class="fas fa-newspaper"></i> Thread</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div v-else class="shop-empty-col">
                <img loading="lazy" :src="asset('assets/illustrations/broadcast-animate.svg')" :alt="'Empty-Trending'">
                <div>
                    <p>Oops! No trending Broadcasts at the moment</p>
                    <a href="/blog/"><i class="fas fa-search"></i> Find exciting broadcasts elsewhere</a>
                </div>
            </div>
        </div>

        <div class="wrapper">
            <div v-if="comments.length <= 0 " class="shop-empty-col">
                <img loading="lazy" :src="asset('assets/illustrations/file-searching-animate.svg')" :alt="'Empty-Comments'">
                <div>
                    <p>Oops! No comments on this Broadcast at the moment</p>
                    <a href="/blog/"><i class="fas fa-search"></i> Find exciting broadcasts elsewhere</a>
                </div>
            </div>
            <div v-else>
                <div class="sb-text">
                    <i class="fas fa-comment"></i> REPLIES
                </div>
                <div class="display-tweets">
                    <broadcast-component v-bind:broadcasts="comments" v-bind:user="user" v-on:viewImage="viewImage" v-on:appendBroadcast="appendBroadcast" v-on:updateComment="updateComment" v-on:showCommentBox="showCommentBox" v-on:showOptionBox="showOptionBox" v-on:showShareBox="showShareBox"></broadcast-component>
                    <infinite-loading @infinite="infiniteHandler"></infinite-loading>
                </div>
            </div>
            
            <events-component v-bind:user="user" v-bind:updateUser="updateUser" v-on:updateComment="updateComment" v-on:closeBroadcast="closeBroadcast" v-bind:createBroadcast="createBroadcast" v-bind:showComment="showComment" v-bind:showOption="showOption" v-bind:showShare="showShare" v-bind:tweet="tweet" v-on:closeComment="closeComment" v-on:closeOption="closeOption" v-on:closeShare="closeShare" v-on:viewImage="viewImage" v-on:updateBroadcasts="updateBroadcasts" v-on:addedBookmark="addedBookmark" v-on:removedBookmark="removedBookmark"></events-component> 
        </div>
    </div>
</template>

<script>
import EventsComponent from './inc/EventsComponent'
import InfiniteLoading from 'vue-infinite-loading';
import BroadcastComponent from './inc/BroadcastComponent';
export default {
    data() {
        return {
            broadcasts:[],
            comments_exist: '',
            form:{},
            comments:[],
            tweet:{},
            showComment: false,
            showShare: false,
            showOption: false,
            status: '',
            type: '',
            to_hide_border: '',
            newBroadcasts:[],
            filterBroadcasts: [],
            tweet_is_original: '',
            api: '',
            page: 1
        }
    },
    props: ["user", "createBroadcast"],
    components:{
        BroadcastComponent,
        EventsComponent,
        InfiniteLoading
    },
    watch:{
        $route(){
            this.status = this.$route.params.status;
            this.broadcasts = [];
            this.comments = [];
            this.page = 1;
            axios.get('/api/broadcast/thread/'+this.$route.params.status)
            .then((res)=>{
                console.log(res.data.data.broadcasts);
                if (res.data.data.status ==200) {
                    if (res.data.data.broadcasts.length > 0) {
                        this.broadcasts = res.data.data.broadcasts;
                        this.broadcasts.forEach((broadcast, index) =>{
                            broadcast.media = broadcast.media != null ? JSON.parse(broadcast.media): null;
                            this.comments_exist = broadcast.comments > 0 ? true: false ;
                            this.form.id =  broadcast.id;
                            if(index == this.broadcasts.length - 1){
                                this.to_hide_border = broadcast.id;
                            }
                        })
                    }
                }
                this.api = '/api/broadcast/'+this.status+'/comments';
                this.infiniteHandler();
            })
            .catch(err=>console.log(err));
        }
    },
    created() {
        this.status = this.$route.params.status;
        axios.get('/api/broadcast/thread/'+this.$route.params.status)
        .then((res)=>{
            if (res.data.data.status ==200) {
                console.log(res.data.data.broadcasts);
                if (res.data.data.broadcasts.length > 0) {
                    this.broadcasts = res.data.data.broadcasts;
                    this.broadcasts.forEach((broadcast, index) =>{
                        broadcast.media = broadcast.media != null ? JSON.parse(broadcast.media): null;
                        this.comments_exist = broadcast.comments > 0 ? true: false ;
                        this.form.id =  broadcast.id;
                        if(index == this.broadcasts.length - 1){
                            this.to_hide_border = broadcast.id;
                        }
                    })
                }
                this.api = '/api/broadcast/'+this.status+'/comments';
                this.infiniteHandler();
            }
        })
        .catch(err=>console.log(err));
    },
    methods: {
        infiniteHandler($state) {
            axios.get(this.api, {
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
                        
                    })
                    this.comments.push(...res.data.data);
                    
                    $state.loaded();
                } else {
                    $state.complete();
                }
            })
            .catch(err=>console.log(err));
        },
        likeBroadcast(id, type){
            axios.post('/api/broadcast/'+id+'/like')
            .then((res)=>{
                console.log(res.data.data);
                this.broadcasts.forEach(element => {
                    if (element.id == id) {
                        element.is_liked = true;
                        element.likes = res.data.data.likes;
                    }
                });
            })
        },
        unlikeBroadcast(id, type){
            axios.post('/api/broadcast/'+id+'/unlike')
            .then((res)=>{
                console.log(res.data.data);
                this.broadcasts.forEach(element => {
                    if (element.id == id) {
                        element.is_liked = false;
                        element.likes = res.data.data.likes;
                    }
                });
            })
        },
        rebroadcast(id, type){
            axios.post('/api/broadcast/'+id+'/rebroadcast')
            .then((res)=>{
                console.log(res.data.data);
                this.broadcasts.forEach(element => {
                    if (element.id == id) {
                        element.is_rebroadcast = true;
                        element.rebroadcasts = res.data.data.rebroadcasts;
                    }
                });
            })
            .catch(err=>console.log(err));    
        },
        undoRebroadcast(id, type){
            axios.post('/api/broadcast/'+id+'/undorebroadcast')
            .then((res)=>{
                console.log(res.data.data);
                this.broadcasts.forEach(element => {
                    if (element.id == id) {
                        element.is_rebroadcast = false;
                        element.rebroadcasts = res.data.data.rebroadcasts;
                    }
                });
            })
            .catch(err=>console.log(err));    
        },

        viewStatus(id, type, e){
            if ($(e.target).closest(".tweeter-img").length === 0 && $(e.target).closest(".tweet-func-div").length === 0 && $(e.target).closest(".tweet-profile-div").length === 0 && $(e.target).closest(".retweeted-by").length === 0 && $(e.target).closest(".broadcast-media img").length === 0 && $(e.target).closest(".tweet-content-div a").length === 0 ) {
                if(this.status != id){
                    this.$router.push({name: 'broadcast', params: {page: 'status', status: id}, query:{t: type}})
                }
                this.status = id;
            }
        },
        
        updateComment(data){
            if(this.tweet_is_original == true){
                this.broadcasts.forEach((element)=>{
                    if (element.id == this.tweet.id) {
                        element.comments += 1;
                    }
                });
                this.tweet_is_original = false;
            }else{
                this.comments.forEach((element)=>{
                    if (this.type == 'origin') {
                        if (element.type == 'comment') {
                            if (element.original_post.id == this.tweet.id) {
                                return element.original_post.comments += 1;
                            }
                        }
                    }
                    if (element.id == this.tweet.id) {
                        return element.comments += 1;
                    }else{
                        if (element.type == 'comment') {
                            if (element.original_post.id == this.tweet.id) {
                                element.comments += 1;
                            }
                        }
                    }
                });
            }
            if(data.comment_id == this.form.id){
                this.comments.unshift(data);
            }
        },
        showOptionBox(data, type){
            if (type == "origin") {
                this.tweet = data;
                this.type = type;
                this.showOption = true;
                this.tweet_is_original = true;
            }else{
                this.tweet = data.tweet;
                this.type = data.type;
                this.showOption = true;
            }
        },
        showCommentBox(data, type){
            if (type == "origin") {
                this.tweet = data;
                this.type = type;
                this.showComment = true;  
                this.tweet_is_original = true;          
            }else{
                this.tweet = data.tweet;
                this.type = data.type;
                this.showComment = true;
            }
        },
        showShareBox(data, type){
            if (type == "origin") {
                this.tweet = data;
                this.type = type;
                this.showShare = true;
                this.tweet_is_original = true;          
            }else{
                this.tweet = data.tweet;
                this.type = data.type;
                this.showShare = true;
            }
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
        addedBookmark(){
            if(this.tweet_is_original == true){
                this.broadcasts.forEach((element)=>{
                    if (element.id == this.tweet.id) {
                        return element.bookmarked = true;
                    }
                });
                this.tweet_is_original = false;
            }else{
                this.comments.forEach((element)=>{
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
            }
            this.closeShare();
        },
        removedBookmark(){
            if(this.tweet_is_original == true){
                this.broadcasts.forEach((element)=>{
                    if (element.id == this.tweet.id) {
                        return element.bookmarked = false;
                    }
                });
                this.tweet_is_original = false;
            }else{
                this.comments.forEach((element)=>{
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
        newBroadcast(){
            this.$emit('newBroadcast');
        },
        closeBroadcast(){
            this.$emit('closeBroadcast');
        },
        viewImage(src){
            this.$emit('viewImage', src);
        },
        appendBroadcast(broadcast){
            if (broadcast.media != null) {
                broadcast.media = JSON.parse(broadcast.media);
            }
            this.broadcasts.unshift(broadcast);
        },
        updateBroadcasts(id){
            if(this.$route.params.page == undefined){
                this.broadcasts = this.broadcasts.filter(broadcast => broadcast.retweeter.id != id);
                this.broadcasts.forEach((broadcast, index) =>{
                    if(broadcast.type == "comment"){
                        if (broadcast.original_post.user_id == id) {
                            broadcast.original_post.is_following = false;
                        }
                    }
                    if (broadcast.user_id == id) {
                        broadcast.is_following = false;
                    }
                    console.log(broadcast.retweeter.id);
                    if(broadcast.retweeter.id == undefined && broadcast.user_id == id){
                        this.broadcasts.splice(index, 1);
                    }
                });
            }
            // this.showOption = false;
            this.closeOption();
        },
        updateUser(user){
            if (this.$route.params.username == this.user.name) {
                console.log(user);
                this.visited.following = user.user.following;
            }
            this.$emit('updateUser', user);
        },
    }
}
</script>

