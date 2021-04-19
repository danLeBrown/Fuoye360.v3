<template>
    <div>
        <div id= "tweet-box" class="vs-wrapper" v-if="createBroadcast" style="display:block;" @click="checkToCloseBroadcast($event)">
            <div class="vs-container" style="margin: 0; padding:0; height:100vh;display:flex;align-items:center;justify-content:center;">
                <div class="vs-content" style="border-radius: 1rem;">
                    <form action="index.html" method="post" style="border-radius: 1rem;" id="broadcast-form" @submit.prevent="newBroadcast($event)">
                        <div class="div-header" style="color: limegreen;">
                            <h3><i class="fas fa-bullhorn"></i> Broadcast</h3>
                            <span class="hr-line"></span>
                        </div>
                        <div class="input-div">
                            <textarea id="new-broadcast" v-model="form.text" name="tweet" cols="30" rows="10" placeholder="What's happening?" maxlength="250" :autofocus="createBroadcast" required></textarea>
                        </div>
                        <div class="broadcast-func-div">
                            <input type="file" name="images[]" id="broadcast-image-input" accept=".jpeg, .jpg, .png" multiple @change="previewImage($event)">
                            <button type="button" ><i class="fas fa-image" @click="triggerClick"></i></button>
                        </div>
                        <div class="image-preview" id="preview-div">
                        </div>
                        <div class="input-div tweet-btn-div">
                            <button type="button" class="closeTweetBox" @click="closeTweetBox">Close</button>
                            <button type="submit" class="tweet-btn" style="min-width: 121.05px;"><i class="fas fa-bullhorn"></i> Broadcast</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id= "share-box" class="vs-wrapper blog-modal" @click="closeBroadcastModal($event)" data-name="Share" v-if="showShare" :style="showShare ? 'display:block;' : '' ">
                <div class="vs-container" style="margin: 0; padding:0; height:100vh;display:flex;align-items:flex-end;justify-content:center;">
                    <div class="vs-content" style="border-radius: 1rem 1rem 0 0;position: relative;">
                        <a href="#l-div" class="share-scroll-down"></a>
                        <h3>What would you like to do?</h3>
                        <form action="" method="post" style="border-radius: 1rem 1rem 0 0">
                            <div class="input-div">
                                <button v-if="!tweet.bookmarked" type="button" class="add-to-bookmark" @click="addToBookmarks(tweet.id)"><i class="fas fa-bookmark"></i> Add to Bookmark</button>
                                <button v-else type="button" class="remove-from-bookmark" @click="removeFromBookmarks(tweet.id)"><i class="fas fa-times"></i> Remove from Bookmark</button>
                            </div>
                            <div class="input-div">
                                <button type="button" class="share-broadcast"><i class="fas fa-share-alt"></i> Share Broadcast</button>
                            </div>
                            <div class="input-div">
                                <button type="button" class="share-copy-link"><i class="fas fa-link"></i> Copy link</button>
                            </div>
                            <div id="l-div"></div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div id= "options-box" class="vs-wrapper blog-modal" data-name="Option" @click="closeBroadcastModal($event)" v-if="showOption" :style="showOption ? 'display:block;' : '' ">
                <div class="vs-container" style="margin: 0; padding:0; height:100vh;display:flex;align-items:flex-end;justify-content:center;">
                    <div class="vs-content" style="border-radius: 1rem 1rem 0 0;position: relative;">
                        <a href="#l-div" class="share-scroll-down"></a>
                        <h3>What would you like to do?</h3>
                        <form action="" method="post" style="border-radius: 1rem 1rem 0 0">
                            <div v-if="tweet.user_id == user.id">
                                <button type="button" class="delete-broadcast" @click="deleteBroadcast(tweet.id)"><i class="fas fa-trash"></i> Delete broadcast</button>
                            </div>
                            <div v-if="tweet.user_id != user.id">
                                <div class="input-div">
                                    <button type="button" v-if="tweet.is_following == true" class="report-broadcast" @click="unflwUser(tweet.user_id)"><i class="fas fa-times"></i> Unfollow <b>{{tweet.user.name}}</b></button>
                                    <button type="button" v-else class="report-broadcast" @click="flwUser(tweet.user_id)"><i class="fas fa-plus"></i> Follow <b>{{tweet.user.name}}</b></button>
                                </div>
                                <div class="input-div">
                                    <button type="button" class="report-broadcast" @click="reportBroadcast(tweet.id)"><i class="fas fa-flag"></i> Report Broadcast</button>
                                </div>
                            </div>
                            <div id="l-div"></div>
                        </form>
                    </div>
                </div>
            </div>
            <div id="comment-box" class="vs-wrapper" data-name="Comment" @click="closeBroadcastModal($event)" v-if="showComment" :style="showComment ? 'display:block;' : '' ">
                <div class="vs-container">
                    <div class="vs-content" style="">
                        <div class="div-header" style="color: limegreen;">
                            <h3><i class="fas fa-comment"></i> REPLY </h3>
                        </div>
                        <div class="tweet-div" style="margin-top: .5rem; border:none;">
                            <div class="tweet-content-div">
                                <div style="display: grid;height:100%;">
                                    <img :src="asset('storage/profile_images/'+tweet.user.image)" :alt="tweet.user.name" class="tweeter-img comment-tweeter-img" style="height: 35px;width:35px;">
                                </div>
                                <div class="tweet-txt-div">
                                    <div class="tweet-profile-div">
                                        <span class="tweet-username">{{tweet.user.name}}</span><span class="tweet-time">. {{tweet.timeago}}</span>
                                    </div>
                                    <div class="tweet-body" v-html="tweet.body">
                                    </div>
                                    <div class="broadcast-media" v-if="tweet.media != null">
                                        <img :src="asset('storage/broadcast_images/'+img)" alt="" v-for="img in tweet.media" v-bind:key="img.id" load="lazy" @click="viewImage(asset('storage/broadcast_images/'+img))">
                                    </div>
                                </div>                                
                            </div>
                        </div>
                        <form action="" id="comment-form">
                            <div class="tweet-div" style="margin-top: .5rem;">
                                <div class="tweet-content-div">
                                    <div>
                                        <img :src="asset('storage/profile_images/'+user.image)" :alt="user.name" class="tweeter-img" style="height: 35px;width:35px;">
                                    </div>
                                
                                    <div class="tweet-txt-div">
                                        <textarea name="" id="tweet-comment" rows="10" placeholder="What's on your mind?" @input="checkComment" v-model="comment.body"></textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div style="display: grid;grid-template-columns:repeat(2,1fr);grid-gap:.5rem;padding:.5rem;">
                            <button type="button" @click="closeComment">Close</button>
                            <button type="submit" style="background: limegreen;color:#fff;" id="post-comment" :disabled="!comment.submit" :style=" !comment.submit? 'opacity:.75;': ''" @click.prevent="newComment"><i class="fas fa-comment"></i> Reply</button>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</template>
<script>
export default {
    props: ["user", "createBroadcast", "showShare", "showComment", "showOption", "tweet"],
    data() {
        return {
            comment:{
                submit:false,
                body: '',
                blogger_id: '',
                post_id: '',
                comment_id: '',
            },
            form:{
                id: '',
                broadcast: '',
                page: '',
                receiverid: '',
                action: '',
            },
            modal: ''
        }
    },
    watch:{
        tweet: function (data) {
            this.comment.blogger_id = data.user_id;
            this.comment.post_id = data.id
            if(data.post_id == null && data.user_id == this.user.id){
                this.$emit('createdThread', true);
            }
        }
    },
    methods: {
        addToBookmarks(id){
            axios.post('/api/broadcast/'+id+'/add-bookmark')
            .then(res=>{
                console.log(res.data.data);
                this.$emit('addedBookmark');
            })
            .catch(err=>console.log(err))
        },
        removeFromBookmarks(id){
            axios.post('/api/broadcast/'+id+'/remove-bookmark')
            .then(res=>{
                console.log(res.data.data);
                this.$emit('removedBookmark');
            })
            .catch(err=>console.log(err))
        },
        closeBroadcastModal(e){
            if ($(e.target).closest(".vs-content").length === 0) {
                $(e.target).closest(".vs-wrapper").fadeToggle();
                this.modal = $(e.target).closest(".vs-wrapper").data('name');
                this.$emit('close'+this.modal);
            }
        },
        closeTweetBox(){
            this.$emit('closeBroadcast');
        },
        checkToCloseBroadcast(e){
            if ($(e.target).closest(".vs-content").length === 0) {
                if (e.target.id == 'comment-box') {
                    this.comment.body = '';
                }
                this.$emit('closeBroadcast');
            }
        },
        newBroadcast(e){
            $(".tweet-btn").css('opacity', '.75').html('<span class="loading-circle "></span>').attr("disabled", true);
            var fd = new FormData(e.target);
            axios.post('/api/broadcast', fd, {
                headers:{
                    'Content-Type': 'multipart/form-data' 
                }
            })
            .then((res)=>{            
                console.log(res.data.data);
                this.$emit('appendBroadcast', res.data.data.broadcast);
                this.$emit('closeBroadcast');
                this.form.text = '';
                $(".tweet-btn").css('opacity', '1').html('<i class="fas fa-bullhorn"></i> Broadcast').attr("disabled", false);
            })
            .catch(err=>{console.log(err)});
        
        },
        checkComment(){
            if(this.comment.body.length <= 0){
                this.comment.submit = false;
            }else{
                this.comment.submit = true;
            }
        },
        closeComment(){
            this.comment.body = '';
            $("#comment-box").hide();
            this.$emit("closeComment");
        },
        unflwUser(id){
            axios.post('/api/account/'+id+'/unfollow')
            .then((res)=>{
                this.$emit('updateUser', {user: {
                    following : res.data.data.sender_count
                }});
                this.$emit('updateBroadcasts', id);
                this.tweet.is_following = false;
            })
            .catch(err=>console.error(err));
        },
        flwUser(id, name, page, event){
            axios.post('/api/account/'+id+'/follow')
            .then((res)=>{
                this.$emit('updateUser', {user: {
                    following : res.data.data.sender_count
                }});
                this.$emit('closeOption');
                this.tweet.is_following = true;
            })
            .catch(err=>console.error(err));
        },
        triggerClick(){
            $("#broadcast-image-input").click();
        },
        previewImage(e){
            $(".image-preview").empty();
            var files = e.target.files;
            if (files) {
                if(files.length > 2){
                    alert('You can only upload a maximum of 2 images. Happy Broadcasting!');
                    $("button[type=submit]").attr('disabled', 'disabled');
                }else{
                    for (var i = 0; i < files.length; i++) {
                        var file = files[i];
                        var reader = new FileReader();
                        reader.addEventListener("load", function (e) {
                            var pic = e.target
                            var image = document.createElement('img');
                            $(image).attr('src', pic.result).addClass("preview");
                            $(".image-preview").append(image, null);
                        })
                        reader.readAsDataURL(file);
                    }
                }
            }
        },
        newComment(e){
            $(e.target).css('opacity', '.75').html('<span class="loading-circle "></span> REPLYING...');
            axios.post('/api/broadcast/comment', this.comment)
            .then(res=>{
                console.log(res);
                if(res.data.data.status == 200){
                    this.$emit('updateComment', res.data.data.comment);
                }
                this.closeComment();
                $(e.target).css('opacity', '1').html('<i class="fas fa-comment"></i> REPLY');
            })
            .catch(err=>console.log(err));
        },
        
        viewImage(src){
            this.$emit('viewImage', src);
        },
    },
}

</script>
