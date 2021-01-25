<template>
    <div>
        <div v-if="broadcasts.length > 0">
            <div v-for="broadcast in broadcasts" v-bind:key="broadcast.id" :data-href="'broadcast/status/'+broadcast.id" class="view-thread tweet-div" :data-status="broadcast.id">
                <div class="tweet-content-div for-thread" v-if="broadcast.is_comment && !broadcast.is_thread" @click="viewStatus(broadcast.original_post.id, broadcast.original_post.type, $event )" :style="'transform: scale(.9)'">
                    <div class="thread-border"><span class="border-content"></span></div>
                    <input v-if="broadcast.original_post.user_id != user.id" hidden class="" style="display: none;" value="">
                    <div class="tweet-img-div">
                        <router-link :to="{name: 'visit', params:{username: broadcast.original_post.user.name}, query:{v:'broadcast'}}"><img loading="lazy" :src="asset('storage/profile_images/'+broadcast.original_post.user.image)" :alt="broadcast.original_post.user.name" class="tweeter-img"></router-link>
                    </div>
                    <div class="tweet-txt-div">
                        <div v-if="broadcast.original_post.rebroadcasts != 0 && !broadcast.original_post.thread" class="retweeted-by" style="font-size:.75rem;">
                            <div v-if="broadcast.original_post.retweeter_id == user.id">
                                <i class="fas fa-bullhorn"></i> You retweeted
                            </div>
                            <div v-else-if="broadcast.original_post.info_display == 'retweets'">
                                <div v-if="broadcast.original_post.retweets_count == 1">
                                    <i class="fas fa-bullhorn"></i> {{broadcast.original_post.retweeter.name}}
                                </div>
                                <div v-else-if="broadcast.original_post.retweets_count == 2">
                                    <i class="fas fa-bullhorn"></i> {{broadcast.original_post.retweeter.name}} and 1 other
                                </div>
                                <div v-else-if="broadcast.original_post.retweets_count > 2">
                                    <i class="fas fa-bullhorn"></i> {{broadcast.original_post.retweeter.name}} and {{broadcast.original_post.retweets_count}} others
                                </div>
                            </div>
                            <div v-else-if="broadcast.original_post.info_display == 'likes'" class="tweet-liked" style="font-size:.75rem;">
                                <div v-if="broadcast.original_post.likes_count == 1">
                                    <i class="fas fa-heart"></i> {{broadcast.original_post.retweeter.name}}
                                </div>
                                <div v-else-if="broadcast.original_post.likes_count == 2">
                                    <i class="fas fa-heart"></i> {{broadcast.original_post.retweeter.name}} and 1 other
                                </div>
                                <div v-else-if="broadcast.original_post.likes_count > 2">
                                    <i class="fas fa-heart"></i> {{broadcast.original_post.retweeter.name}} and {{broadcast.original_post.likes_count}} others
                                </div>
                            </div>
                        </div>
                        <div class="tweet-profile-div">
                            <button class="tweet-options" @click="showOptionBox(broadcast.original_post, 'origin')"><i class="fas fa-angle-down"></i></button>
                            <span class="tweet-username">{{broadcast.original_post.user.name}}</span><span class="tweet-time">. {{broadcast.original_post.timeago}}
                            </span>
                        </div>
                        <div v-html="broadcast.original_post.body" class="tweet-body" style="text-decoration: none;color:#000;">
                        </div>
                        <div class="broadcast-media" v-if="broadcast.original_post.media != null">
                            <img loading="lazy" :src="asset('storage/broadcast_images/'+img)" alt="" v-for="img in broadcast.original_post.media" v-bind:key="img.id" load="lazy" @click="viewImage(asset('storage/broadcast_images/'+img))">
                        </div>
                        <div class="tweet-func-div">
                            <button class="" @click="showCommentBox(broadcast.original_post, 'origin')"><i class="far fa-comment"></i> <span class="comment-count">{{broadcast.original_post.comments}}</span></button>
                            <button v-if="broadcast.original_post.is_rebroadcast" class="btn_retweet_broadcast.original_post.id}} tweet-retweet" @click="undoRebroadcast(broadcast.original_post.id, 'origin')"><i class="fa fa-bullhorn"></i> {{broadcast.original_post.rebroadcasts}}</button>
                            <button v-else class="btn_retweet_broadcast.original_post.id}}" @click="rebroadcast(broadcast.original_post.id, 'origin')"><i class="fa fa-bullhorn"></i> {{broadcast.original_post.rebroadcasts}}</button>
                            <button v-if="broadcast.original_post.is_liked" class="btn_like_broadcast.original_post.id}} tweet-liked" @click="unlikeBroadcast(broadcast.original_post.id, 'origin')"><i class="fas fa-heart"></i> {{broadcast.original_post.likes}}</button>
                            <button v-else class="btn_like_broadcast.original_post.id}}" @click="likeBroadcast(broadcast.original_post.id, 'origin')"><i class="far fa-heart"></i> {{broadcast.original_post.likes}}</button>                        
                            <button @click="showShareBox(broadcast.original_post, 'origin')"><i class="fas fa-share-alt"></i></button>
                        </div>
                        <span v-if="broadcast.original_post.is_thread" style="background:var(--brand-color);padding:.25rem .5rem;color:var(--white-color);border-radius:1rem;font-size:.75rem;"><i class="fas fa-newspaper"></i> Thread</span>
                    </div>
                </div>
                <div class="tweet-content-div" @click="viewStatus(broadcast.id, broadcast.type, $event)">
                    <input v-if="broadcast.user_id != user.id" hidden class="" style="display: none;" value="">
                    <div class="tweet-img-div">
                        <router-link :to="{name: 'visit', params:{username: broadcast.user.name}, query:{v:'broadcast'}}"><img loading="lazy" :src="asset('storage/profile_images/'+broadcast.user.image)" :alt="broadcast.user.name" class="tweeter-img"></router-link>
                    </div>
                    <div class="tweet-txt-div">
                        <div v-if="broadcast.rebroadcasts != 0 && !broadcast.thread" class="retweeted-by" style="font-size:.75rem;">
                            <div v-if="broadcast.retweeter_id == user.id">
                                <i class="fas fa-bullhorn"></i> You retweeted
                            </div>
                            <div v-else-if="broadcast.info_display == 'retweets'">
                                <div v-if="broadcast.retweets_count == 1">
                                    <i class="fas fa-bullhorn"></i> {{broadcast.retweeter.name}}
                                </div>
                                <div v-else-if="broadcast.retweets_count == 2">
                                    <i class="fas fa-bullhorn"></i> {{broadcast.retweeter.name}} and 1 other
                                </div>
                                <div v-else-if="broadcast.retweets_count > 2">
                                    <i class="fas fa-bullhorn"></i> {{broadcast.retweeter.name}} and {{broadcast.retweets_count}} others
                                </div>
                            </div>
                            <div v-else-if="broadcast.info_display == 'likes'" class="tweet-liked" style="font-size:.75rem;">
                                <div v-if="broadcast.likes_count == 1">
                                    <i class="fas fa-heart"></i> {{broadcast.retweeter.name}}
                                </div>
                                <div v-else-if="broadcast.likes_count == 2">
                                    <i class="fas fa-heart"></i> {{broadcast.retweeter.name}} and 1 other
                                </div>
                                <div v-else-if="broadcast.likes_count > 2">
                                    <i class="fas fa-heart"></i> {{broadcast.retweeter.name}} and {{broadcast.likes_count}} others
                                </div>
                            </div>
                        </div>
                        <div class="tweet-profile-div">
                            <button class="tweet-options" @click="showOptionBox(broadcast)"><i class="fas fa-angle-down"></i></button>
                            <span class="tweet-username">{{broadcast.user.name}}</span><span class="tweet-time">. {{broadcast.timeago}}
                            </span>
                        </div>
                        <div v-html="broadcast.body" class="tweet-body" style="text-decoration: none;color:#000;">
                        </div>
                        <div class="broadcast-media" v-if="broadcast.media != null">
                            <img loading="lazy" :src="asset('storage/broadcast_images/'+img)" alt="" v-for="img in broadcast.media" v-bind:key="img.id" load="lazy" @click="viewImage(asset('storage/broadcast_images/'+img))">
                        </div>
                        <div class="tweet-func-div">
                            <button class="" @click="showCommentBox(broadcast, broadcast.type)"><i class="far fa-comment"></i> <span class="comment-count">{{broadcast.comments}}</span></button>
                            <button v-if="broadcast.is_rebroadcast" class="btn_retweet_broadcast.id}} tweet-retweet" @click="undoRebroadcast(broadcast.id)"><i class="fa fa-bullhorn"></i> {{broadcast.rebroadcasts}}</button>
                            <button v-else class="btn_retweet_broadcast.id}}" @click="rebroadcast(broadcast.id)"><i class="fa fa-bullhorn"></i> {{broadcast.rebroadcasts}}</button>
                            <button v-if="broadcast.is_liked" class="btn_like_broadcast.id}} tweet-liked" @click="unlikeBroadcast(broadcast.id)"><i class="fas fa-heart"></i> {{broadcast.likes}}</button>
                            <button v-else class="btn_like_broadcast.id}}" @click="likeBroadcast(broadcast.id)"><i class="far fa-heart"></i> {{broadcast.likes}}</button>                        
                            <button @click="showShareBox(broadcast)"><i class="fas fa-share-alt"></i></button>
                        </div>
                        <span v-if="broadcast.is_thread" style="background:var(--brand-color);padding:.25rem .5rem;color:var(--white-color);border-radius:1rem;font-size:.75rem;"><i class="fas fa-newspaper"></i> Thread</span>
                    </div>
                </div>                    
            </div>
        </div>
        <div v-else-if="$route.name == 'visit'" class="wrapper">
            <div class="shop-empty-col">
                <img loading="lazy" :src="asset('assets/illustrations/broadcast-animate.svg')" alt="Empty-Broadcasts">
                <div>
                    <p>Oops! {{visited.name}} has no broadcasts yet.</p>
                    Broadcasts sent by {{visited.name}} will appear here
                    <router-link :to="{name: 'broadcast', params:{page: 'trending'}}"><i class="fas fa-search"></i> Find other exciting broadcasts</router-link>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            newbroadcasts:[],
            filterbroadcasts: [],
            tweet:{},
            status: '',
        }
    },
    props: ["user", "broadcasts", "visited", "createBroadcast"],
    methods: {
        viewImage(src){
            this.$emit('viewImage', src);
        },
        viewStatus(id, type, e){
            if ($(e.target).closest(".tweeter-img").length === 0 && $(e.target).closest(".tweet-func-div").length === 0 && $(e.target).closest(".tweet-profile-div").length === 0 && $(e.target).closest(".retweeted-by").length === 0 && $(e.target).closest(".broadcast-media img").length === 0 && $(e.target).closest(".tweet-content-div a").length === 0 ) {
                if(this.status != id){
                    this.$router.push({name: 'broadcast', params: {page: 'status', status: id}, query:{t: type}})
                }
                this.status = id;
            }
        },
        likeBroadcast(id, type){
            axios.post('/api/broadcast/'+id+'/like')
            .then((res)=>{
                console.log(res.data.data);
                this.broadcasts.forEach(element => {
                    if (type == 'origin') {
                        if (element.type == 'comment') {
                            if (element.original_post.id ==id) {
                                element.original_post.is_liked = true;
                                element.original_post.likes = res.data.data.likes;;
                            }
                        }
                    }
                    if (element.id == id) {
                        element.is_liked = true;
                        element.likes = res.data.data.likes;
                    }else{
                        if (element.type == 'comment') {
                            if (element.original_post.id ==id) {
                                element.original_post.is_liked = true;
                                element.original_post.likes = res.data.data.likes;;
                            }
                            
                        }
                    }

                });
            })
        },
        unlikeBroadcast(id, type){
            axios.post('/api/broadcast/'+id+'/unlike')
            .then((res)=>{
                console.log(res.data.data);
                this.broadcasts.forEach(element => {
                    if (type == 'origin') {
                        if (element.type == 'comment') {
                            if (element.original_post.id ==id) {
                                element.original_post.is_liked = false;
                                element.original_post.likes = res.data.data.likes;;
                            }
                        }
                    }
                    if (element.id == id) {
                        element.is_liked = false;
                        element.likes = res.data.data.likes;
                    }else{
                        if (element.type == 'comment') {
                            if (element.original_post.id ==id) {
                                element.original_post.is_liked = false;
                                element.original_post.likes = res.data.data.likes;;
                            }
                            
                        }
                    }
                });
            })
        },
        rebroadcast(id, type){
            axios.post('/api/broadcast/'+id+'/rebroadcast')
            .then((res)=>{
                console.log(res.data.data);
                this.broadcasts.forEach(element => {
                    if (type == 'origin') {
                        if (element.type == 'comment') {
                            if (element.original_post.id ==id) {
                                element.original_post.is_rebroadcast = true;
                                element.original_post.rebroadcasts = res.data.data.rebroadcasts;;
                            }
                        }
                    }
                    if (element.id == id) {
                        element.is_rebroadcast = true;
                        element.rebroadcasts = res.data.data.rebroadcasts;
                    }else{
                        if (element.type == 'comment') {
                            if (element.original_post.id ==id) {
                                element.original_post.is_rebroadcast = true;
                                element.original_post.rebroadcasts = res.data.data.rebroadcasts;;
                            }
                        }
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
                    if (type == 'origin') {
                        if (element.type == 'comment') {
                            if (element.original_post.id ==id) {
                                element.original_post.is_rebroadcast = false;
                                element.original_post.rebroadcasts = res.data.data.rebroadcasts;;
                            }
                        }
                    }
                    if (element.id == id) {
                        element.is_rebroadcast = false;
                        element.rebroadcasts = res.data.data.rebroadcasts;
                    }else{
                        if (element.type == 'comment') {
                            if (element.original_post.id ==id) {
                                element.original_post.is_rebroadcast = false;
                                element.original_post.rebroadcasts = res.data.data.rebroadcasts;;
                            }
                        }
                    }
                });
            })
            .catch(err=>console.log(err));    
        },

        /**
         * emit functions
         */

        showOptionBox(data, type){
            this.broadcasts.forEach((element)=>{
                if (type == 'origin') {
                    if (element.type == 'comment') {
                        if (element.original_post.id == data.id) {
                           return this.tweet = element.original_post;
                        }
                    }
                }
                if (element.id == data.id) {
                    return this.tweet = element;
                }
            });
            this.$emit('showOptionBox', {tweet: this.tweet, type: type});
        },
        showCommentBox(data, type){
            this.broadcasts.forEach((element)=>{
                if (type == 'origin') {
                    if (element.type == 'comment') {
                        if (element.original_post.id == data.id) {
                           return this.tweet = element.original_post;
                        }
                    }
                }
                if (element.id == data.id) {
                    return this.tweet = element;
                }
            });
            this.$emit('showCommentBox', {tweet: this.tweet, type: type});
        },
        showShareBox(data, type){
            this.broadcasts.forEach((element)=>{
                if (type == 'origin') {
                    if (element.type == 'comment') {
                        if (element.original_post.id == data.id) {
                           return this.tweet = element.original_post;
                        }
                    }
                }
                if (element.id == data.id) {
                    return this.tweet = element;
                }
            });
            this.$emit('showShareBox', {tweet: this.tweet, type: type});
        },
    },
}
</script>

<style>
.for-thread{
    position: relative;
}
.thread-border{
    height: 100%;
    display: flex;
    align-items: center;
    position: absolute;
    width: 2px;
    left: 26px;
}

.thread-border .border-content{
    width: inherit;
    height: 95%;
    background: #eee;
    z-index: -1;
}
#comment-box button{
    border: 2px solid limegreen;
    padding: .5rem;
    border-radius:1rem;
    background: none;
    color: limegreen;
    font-weight: bold; 
    text-transform: uppercase;
}

.display-tweets {
    background: var(--white-color);
}

.hr-line {
    content: '';
    margin-left: .5rem;
    width: 100%;
    height: 2px;
    background: var(--brand-color);
}

.tweet-div {
    padding: .5rem;
    border-bottom: 1px solid #eee;
}

.tweet-content-div {
    display: flex;
    align-items: flex-start;
    z-index: 1;
}
.tweet-profile-div {
    position: relative;
}

.tweet-options {
    position: absolute;
    right: .5rem;
    top: 0;
    color: #ccc;
    background: none;
    border: none;
    padding: .5rem;
}

.tweet-time {
    color: #ccc;
    font-size: .8rem;
}

.tweet-img-div{
    z-index: 1;
    padding: .25rem 0;
    background: var(--white-color);
}
.tweet-div .tweeter-img {
    width: 55px;
    height: 55px;
    border-radius: 50%;
    object-fit: cover;
    background-color: var(--bg-color);
}

.tweet-username {
    font-weight: bold;
}

.tweet-profile-div span {
    margin-left: .25rem;    
    word-break: break-all;
}

.tweet-txt-div {
    margin-left: .5rem;
    width: 100%;
}

.tweet-txt {
    margin-top: .5rem;
}

.tweet-func-div {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    padding: .25rem 0;
}

.tweet-func-div button {
    font-size: inherit;
    background: none;
    color: #ccc;
    padding: .25rem;
    border: none;
    border-radius: .5rem;
    outline: none;
}

.tweet-div:last-child {
    border: none;
}
.tweet-body a{
    color: var(--brand-color);
}
#tweet-box form {
    background: none;
}

#tweet-box form .input-div {
    width: 100%;
}

#tweet-box textarea {
    width: inherit;
    border: none;
    resize: none;
    font: inherit;
    outline: none;
}

#tweet-box .tweet-btn-div {
    display: flex;
    justify-content: flex-end;
}

.tweet-btn-div button {
    border: none;
    border-radius: .5rem;
    padding: .5rem 1rem;
    font: inherit;
    margin: .5rem .25rem .5rem 0;
    border: 2px solid var(--brand-color);
    background: var(--white-color);
    color: var(--brand-color);
}

.tweet-btn-div .tweet-btn {
    background: var(--brand-color);
    color: var(--white-color);
    font-weight: bold;
}

.tweet-liked i {
    color: var(--red-color);
}

.retweeted-by,
.tweet-retweet i {
    color: var(--brand-color);
    text-decoration: none;
    word-break: break-all;
}
.retweeted-by{
    font-size: .75rem;
}
.blog-modal,
#comment-box {
    display: none;
}

.image-preview .preview{
    object-fit: cover;
}

#preview-div img{
    margin: .5rem;
}
.broadcast-func-div input[type=file]{
    display: none;
}
.broadcast-func-div button{
    font-size: inherit;
    font-size: 1.2rem;
    background: none;
    color: var(--brand-color);
    border: 1px solid var(--brand-color);
    border: none;
    padding: .25rem;
}
.blog-modal .share-scroll-down {
    content: '';
    position: absolute;
    margin: auto;
    left: 0;
    right: 0;
    top: 5px;
    display: block;
    width: 15%;
    height: 8px;
    background: var(--bg-color);
    border-radius: .5rem;
    -webkit-border-radius: .5rem;
    -moz-border-radius: .5rem;
    -ms-border-radius: .5rem;
    -o-border-radius: .5rem;
}

.blog-modal h3 {
    margin: 1rem 0;
    text-align: center;
}

.blog-modal form {
    padding: 1rem;
}

.blog-modal .input-div {
    width: 100%;
}

.blog-modal button {
    padding: .8rem .5rem;
    font: inherit;
    width: inherit;
    text-align: left;
    background: none;
    border: none;
}
#comment-box textarea{
    max-width: inherit; 
    outline:none;
    border:none;
    resize:none;
    background:none;
    width:100%;
    font:inherit;
}
</style>