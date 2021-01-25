<template>
    <div>
        <section id="profile-wrap" class="vs-wrapper profile-hide" @click="toggleProfileState($event)">
            <div class="profile-help">
                <span class="phelp-content"><i class="fas fa-info"></i></span>
            </div>
            <div class="vs-container">
                <div class="profile-card vs-content">
                    <a href="#l-div" class="share-scroll-down"></a>
                    <!-- <br> -->
                    <div class="u-container" style="margin-top:.5rem;">
                    </div>
                    <div class="l-container">
                        <div class="profile-info">
                            <div class="dynamic-div">
                                <div class='image-container'>
                                    <img loading="lazy" :src="asset('storage/profile_images/'+user.image)" :alt="user.name" class="profile-display"/>
                                </div>
                                <div id='user-info' class='user-info-hide'>
                                    <div class="" style="margin-bottom: .5rem;">
                                    <label class='success shop-label'>USER</label> <span id="username" class=''>{{user.name}}</span>
                                    </div>
                                    <div class="" style="margin-bottom: .5rem;">
                                        <label class='success shop-label'>LOCATION</label> <span class='location'>{{user.geo_location}}</span>
                                    </div>
                                    <div class="" style="margin-bottom: .5rem;">
                                        <label class='success shop-label'>PHONE NO</label> <span class='telephone'>{{user.telephone}}</span>
                                    </div>
                                    <div class="">
                                        <label class='success shop-label' style="display:none ;">BIO</label>
                                        <div class="bio-display bio" style="line-height: 1.4em; color:grey;">
                                            {{user.bio == ''? 'Enter Bio here' : user.bio}}
                                        </div>
                                    </div>
                                    <button class='profile-btn' id="edit-btn" @click="showEditProfile">edit profile
                                    </button>
                                </div>
                            </div>
                            <div style="text-align:center;">
                                <router-link :to="{name: 'visit', params: {username: user.name}, query:{v:'following'}}" style="text-decoration:none;color:#333;"><span id= "a-following" style="font-weight:bold;">{{user.following}}</span> Following</router-link> <router-link :to="{name: 'visit', params: {username: user.name}, query:{v:'follower'}}" style="margin-left:.5rem;text-decoration:none;color:#333;"><span id= "a-followers" style="font-weight:bold;">{{user.followers}}</span> Followers</router-link>
                                <div class="u-total-views"><i class="fas fa-eye"></i> {{user.total_views}} views</div>
                                <div class="u-total-"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i> </div>
                            </div>
                            <div class="profile-bar">
                                <div class="profile-bar" style="border-bottom:1px solid var(--input-border-color);padding:.25rem 0;">
                                    <router-link :to="{name: 'ad'}" :class="$route.params.page == 'ad' ? 'profileNav-selected': '' " class="create-ad"><i class='fas fa-ad' style='position:relative;'></i> Create Ad</router-link>
                                    <router-link :to="{name: 'notifications'}" :class="$route.params.page == 'notifications' ? 'profileNav-selected': '' " class="create-ad"><i class='fas fa-bell'></i> Notifications</router-link>
                                </div>
                                <div  v-if="$route.name == 'shop' || $route.query.v == 'shop'" class="profile-bar">
                                    <router-link :to="{name: 'shop', params:{page : 'buy'}}" :class="$route.params.page == 'buy' ? 'profileNav-selected': '' "><i class='fas fa-cart-plus'></i> Buy</router-link>
                                    <router-link :to="{name : 'shop'}" :class="$route.params.page == undefined ? 'profileNav-selected': '' "><i class='fas fa-plus'></i> Following</router-link>
                                    <router-link :to="{name : 'shop', params:{page : 'inventory'}}" :class="$route.params.page == 'inventory' ? 'profileNav-selected': '' "><i class='fas fa-store'></i> Inventory</router-link>
                                    <router-link :to="{name : 'shop', params:{page : 'cart'}}" :class="$route.params.page == 'cart' ? 'profileNav-selected': '' "><i class='fas fa-shopping-cart'></i> Cart</router-link>
                                    <!-- <router-link :to="{name : 'shop', params:{page : 'sell'}}" :class="$route.params.page == 'sell' ? 'profileNav-selected': '' "><i class='fas fa-wallet'></i> Sell</router-link> -->
                                    <router-link :to="{name: 'shop', params:{page : 'wishlist'}}" :class="$route.params.page == 'wishlist' ? 'profileNav-selected': '' "><i class='fas fa-bookmark'></i> Wishlist</router-link>
                                </div>
                                <div v-else-if="$route.name == 'broadcast' || $route.query.v == 'broadcast'" class="profile-bar">
                                    <button type="button" class="blog-broadcast" @click="newBroadcast"><i class='fas fa-bullhorn'></i> Broadcast</button>
                                    <router-link :to="{name: 'broadcast'}" :class="$route.params.page == undefined ? 'profileNav-selected': '' "><i class='fas fa-plus'></i> Following</router-link>
                                    <router-link :to="{name: 'broadcast', params :{page: 'trending'}}" :class="$route.params.page == 'trending' ? 'profileNav-selected': '' "><i class='fas fa-fire'></i> Trending</router-link>
                                    <router-link :to="{name: 'broadcast', params :{page: 'bookmarks'}}" :class="$route.params.page == 'bookmarks' ? 'profileNav-selected': '' "><i class='fas fa-bookmark'></i> Bookmarks</router-link>
                                </div>
                                <a href="#" @click.prevent="logout" class="logout-form"> <i class="fas fa-sign-out-alt"></i> Logout </a>
                            </div>
                        </div>
                    </div>    
                </div>
            </div>
        </section>
        <section class="vs-wrapper profile-form-hide" id="edit-profile-div">
            <div class="vs-container">
                <div class="vs-content">
                    <form action="#" class="profile-form" enctype="multipart/form-data" method="POST" @submit.prevent="editProfile($event)">
                        <div class="profile-form-header">
                            <button type="button" class="close-profile-btn close-profile-form">&times;</button>
                            <h3 class="">Edit Profile</h3>
                            <button type="submit" class="profile-form-submit" :disabled="form.location.length <= 0 || form.telephone.length <= 0" :style="form.location.length <= 0 || form.telephone.length <= 0? 'opacity: .75;': ''" >SAVE</button>
                        </div>
                        <div class="profile-form-content">
                            <div class="image-container">
                                <input type="file" name="image" style="display:none;" @change="displayImage($event)" id="profile-image" accept=".jpeg, .jpg, .png" />
                                <img loading="lazy" :src="asset('storage/profile_images/'+user.image)" :alt="user.name" class="profile-display" id="profile-display"/>
                                <div class="edit-profile-image-div" @click="triggerclick">  
                                    <button type="button"><i class="fas fa-camera"></i></button>
                                </div>
                                <small>*Click to change your Profile Picture</small>
                            </div><br>    
                            <div class="profile-form-info">
                                <div class="input-div">
                                    <label class='success shop-label'>USER</label> <span class='' style="margin-left: .25rem;font-weight:bold;">{{user.name}}</span>
                                </div>
                                <div class="input-div">
                                    <label class='success shop-label'>LOCATION</label> <input type="text" name="location" id="geo_location" class="glocation" @input="validateForm($event)" v-model="form.location">
                                <div class="form-validation"></div>
                                </div>
                                <div class="input-div">
                                    <label class='success shop-label'>PHONE NO</label> <input class="telephone" type="text" name="telephone" id="mobile_number" @input="validateForm($event)" maxlength="11" v-model="form.telephone">
                                    <div class="form-validation"></div>
                                </div>
                                <div class="profile-bio">
                                    <h3 class='shop-label' style="">BIO</h3>
                                    <div class= "input-div">
                                        <textarea class="bio" name="bio" id="" cols="" rows="" v-model="form.bio">
                                        </textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="close-profile-div">
                                <button type="button" class="close-profile-form">CANCEL</button>
                                <button type="submit" style="background:var(--brand-color);color:white;"  :disabled="form.location.length <= 0 || form.telephone.length <= 0" :style="form.location.length <= 0 || form.telephone.length <= 0? 'opacity: .75;': ''">SAVE</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
export default {
    name: "ProfileComponent",
    props: ["user", "profileState"],
    data() {
        return {
            // form: Vue.util.extend({}, this.user),
            form:{
                name: "",
                bio: "",
                location: "",
                telephone: ""
            },
            image: null,
        }
    },
    created() {
        this.form.name = this.user.name;
        this.form.location = this.user.geo_location;
        this.form.telephone = this.user.telephone;
        this.form.bio = this.user.bio;
    },
    watch:{
        profileState: function(data){
            $(".profile-form-hide").hide();
            if (data == false) {
                $(".profile-hide").fadeOut();
            }else{
                $(".profile-hide").fadeIn();
            }
        }
    },
    mounted(){
        $(".close-profile-form").on("click", function() {
            $(".profile-form-hide").hide();
            $(".profile-hide").show();
        });
    },
    methods: {
        async toggleProfileState(e){
            if ($(e.target).closest(".profile-card").length === 0 && $(e.target).closest(".profile-help").length === 0) {
                await $(".profile-hide").fadeOut();
                if (this.profileState == false) {
                    await this.$emit('toggleProfile', true);
                }else{
                    await this.$emit('toggleProfile', false);
                }
            }
        },
        showEditProfile(){
            $(".profile-form-hide").toggle();
            $(".profile-hide").toggle();
        },
        async editProfile(e){
            $(".profile-form button[type=submit]").html('<span class="loading-circle "></span>');
            var fd = await new FormData(e.target);
            // fd.append('image', this.image)
            // this.form = this.image;
            await axios.post('/api/account/update', fd, 
            {
                headers:{
                    'Content-Type': 'multipart/form-data' 
                }
            }
            )
            .then((res)=>{            
                $(".profile-form-hide").toggle();
                $(".profile-form button[type=submit]").html('SAVE');
                console.log(res.data.data);
                this.$emit('updateProfile', res.data.data.user);
                this.$emit('toggleProfile', false);
            })
            .catch(err=>{console.log(err)});
        },
        logout(){
            this.$emit('logout');
        },
        newBroadcast(){
            $(".profile-hide").fadeToggle(300);
            this.$emit('toggleProfile', false);
            this.$emit('newBroadcast');
        },
        triggerclick(){
            $("#profile-image").click();
        },
        async displayImage(data) {
            console.log(data);
            if (data.target.files[0]) {
                var reader = await new FileReader();
                await reader.readAsDataURL(data.target.files[0]);
                reader.onload = await function(event) {
                    const imgElement = document.createElement("img");
                    imgElement.src = event.target.result;
                    imgElement.onload = function (e) {
                        const canvas = document.createElement("canvas");
                        const MAX_WIDTH = 400;
                        const scaleSize = MAX_WIDTH/ e.target.width;
                        canvas.width = MAX_WIDTH;
                        canvas.height = e.target.height * scaleSize;
                        const ctx = canvas.getContext("2d");
                        ctx.drawImage(e.target, 0, 0, canvas.width, canvas.height);

                        let srcEncoded = ctx.canvas.toDataURL(e.target, "image/jpeg");
                        var profileDisplay = document.querySelectorAll("#profile-display");
                        for (var i = 0; i < profileDisplay.length; i++) {
                            $(profileDisplay[i]).attr("src", srcEncoded);
                        }
                        this.image = JSON.stringify(srcEncoded);
                        console.log(this.image);
                    };
                }
            }
        },
        validateForm(e) {
            let formInput,format;
            formInput = e.target.name;
            if (formInput == "mobile_number") {
                format = /^\d+$/;
                if (format.test(e.target.value) && e.target.value.length == 11) {
                    $(e.target.parentElement).children('.form-validation').removeClass('invalid');
                    $(e.target.parentElement).children('.form-validation').removeClass('i-invalid');
                    $(e.target.parentElement).children('.form-validation').addClass('valid').html("&check;").show();
                } else if (e.target.value == '') {
                    $(e.target.parentElement).children('.form-validation').removeClass('valid').addClass('invalid').html("<i class=\"fas fa-info-circle\"></i>").show();
                } else {
                    $(e.target.parentElement).children('.form-validation').removeClass('i-invalid');
                    $(e.target.parentElement).children('.form-validation').removeClass('valid');
                    $(e.target.parentElement).children('.form-validation').addClass('invalid').html("&times;").show();
                }
            }else if (formInput == "location") {
                format = /[ `!@#$%^&*()+_\-=\[\]{};':"\\|.<>\/?~]/;
                if (e.target.value.substr(-1) == "_" || e.target.value.substr(-1) == " " || e.target.value.substr(1) == " " || e.target.value.substr(0) == "_" || format.test(e.target.value)) {
                    $(e.target.parentElement).children('.form-validation').removeClass('i-invalid');
                    $(e.target.parentElement).children('.form-validation').removeClass('valid');
                    $(e.target.parentElement).children('.form-validation').addClass('invalid').html("&times;").show();
                } else if (e.target.value == '') {
                    $(e.target.parentElement).children('.form-validation').removeClass('valid').addClass('invalid').html("<i class=\"fas fa-info-circle\"></i>").show();
                } else {
                    $(e.target.parentElement).children('.form-validation').removeClass('invalid');
                    $(e.target.parentElement).children('.form-validation').removeClass('i-invalid');
                    $(e.target.parentElement).children('.form-validation').addClass('valid').html("&check;").show();
                }
            } 
        },
    },
}
</script>

<style scoped>
.profile-form-header {
    display: flex;
    padding: .5rem;
    position: relative;
    align-items: center;
}

.profile-form-header h3 {
    margin-left: .5rem;
}

.close-profile-btn {
    color: var(--red-color);
    font: inherit;
    font-size: 1.8rem;
    background: none;
}

.profile-form-submit {
    background: var(--brand-color);
    color: var(--white-color);
    font-weight: bold;
    padding: .5rem;
    border-radius: 1rem;
    width: 35%;
    font-size: inherit;
    border: none;
    position: absolute;
    right: 5px;
}

.profile-form-content .image-container {
    position: relative;
    border-bottom: 2px solid #444;
}

.edit-profile-image-div button {
    border: none;
    color: var(--brand-color);
    width: 80px;
    height: 80px;
    border-radius: 50%;
    position: absolute;
    background-color: rgba(1, 1, 1, 0.15);
    top: 0;
    left: 0;
    right: 0;
    font-size: 1.2rem;
}

.profile-form-info div {
    margin-bottom: .5rem;
}

.profile-form-info input {
    border: none;
    border: 1px solid var(--input-border-color);
    padding: .4rem;
    font: inherit;
    border-radius: .3rem;
}

.profile-form-info sup {
    color: var(--red-color);
    font-size: 1.2rem;
}

.profile-form-content .close-profile-div {
    display: flex;
    justify-content: center;
}

.close-profile-div button {
    border: none;
    border: 2px solid var(--brand-color);
    color: var(--brand-color);
    background: var(--white-color);
    padding: .2rem;
    font: inherit;
    font-weight: bold;
    width: 45%;
    margin-right: .5rem;
    border-radius: 1rem;
}

.profile-bio {
    margin: 0 auto;
}

.profile-bio h3 {
    text-align: center;
    color: var(--brand-color);
    padding: .5rem;
    border-radius: .3rem;
    font: inherit;
    font-weight: bold;
    border: 1px solid var(--brand-color);
}

.profile-bio div {
    display: flex;
    width: inherit;
    margin-top: .3rem;
}

.profile-form-info textarea {
    line-height: 1.4rem;
    border: none;
    padding: .3rem;
    font: inherit;
    border-radius: .3rem;
    resize: none;
    height: 100px;
    border: 1px solid var(--input-border-color);
    margin: 0 auto;
    width: 100%;
}

.profile-help {
    z-index: 99999;
    position: fixed;
    top: 95px;
    right: 25px;
    background: linear-gradient(45deg, tomato, gold);
    border-radius: 50%;
}

.phelp-content {
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
    border: .25rem solid transparent;
    width: 25px;
    height: 25px;
    border-radius: 50%;
    color: var(--white-color);
    cursor: pointer;
}

.phelp-content::before,
.phelp-content::after {
    content: '';
    position: absolute;
    width: 25px;
    height: 25px;
    opacity: 0;
    border-radius: 50%;
    border: .25rem solid #f17f1be7;
    animation: glow 3s linear infinite;
    padding: 1rem;
    -webkit-animation: glow 3s linear infinite;
}

.phelp-content::before {
    animation-delay: 1.4s;
}

@keyframes glow {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        opacity: 1;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

#profile-wrap .profile-card {
    box-sizing: border-box;
    padding: .5em;
    width: 90%;
    max-width: 425px;
    margin: 0 .5rem;
    margin-top: 1rem;
    border-radius: 1rem 1rem 0 0;
    background: var(--white-color);
}

@media screen and (max-width: 425px) {
    #profile-wrap .profile-card {
        width: 100%;
    }
}

.u-container {
    border-radius: 1rem 1rem 0 0;
    background: linear-gradient(45deg, var(--brand-color), #92fe9d);
    height: 100px;
    position: relative;
}

.u-container .profile-h {
    padding: .2rem 0 .2rem .2rem;
}

.profile-h h4 {
    text-transform: uppercase;
    background: #333232;
    border-radius: 1rem 1rem 0 0;
    margin: -.3rem 0 0 -.3rem;
    border: .2rem solid var(--white-color);
    padding: .5rem;
    text-align: center;
}

.l-container {
    background: var(--bg-color);
    padding: .5em;
    position: relative;
}

.l-container #user-info {
    display: grid;
    grid-template-columns: auto;
    grid-gap: .15rem;
    margin-top: 1.2rem;
}

.l-container .shop-label {
    padding: .5rem;
    margin: 0;
    margin-bottom: .5rem;
}

.profile-info {
    border-bottom: 2px solid #eee;
}

#user-info span {
    font-weight: bold;
}

.l-container .image-container {
    position: absolute;
    top: -55px;
    padding: .2em;
    background: var(--white-color);
    width: 80px;
    height: 80px;
    border-radius: 50%;
}

.image-container .profile-display {
    object-fit: cover;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    -webkit-border-radius: 50%;
    -moz-border-radius: 50%;
    -ms-border-radius: 50%;
    -o-border-radius: 50%;
}

.l-container h3,
.l-container h4 {
    box-sizing: border-box;
    font-weight: lighter;
}


#user-info .bio-display {
    padding: .4em;
    line-height: 2em;
    margin: 0 auto;
    word-wrap: break-word;
    border-bottom: 2px solid rgba(117, 116, 116, .5);
}

#user-info .bio:focus {
    box-shadow: none;
    border-bottom: 2px solid green;
}

#user-info .l-container .s_f {
    display: grid;
    grid-template-columns: 1fr 1fr;
    text-align: center;
    font-weight: bold;
    color: #131313cc;
}

.profile-form label {
    font: inherit;
    font-weight: 700;
}

.profile-form .input-div {
    position: relative;
    white-space: nowrap;
    width: inherit;
    display: flex;
    align-items: center;
}

.profile-form .input-div input {
    display: inline-block;
    width: 100%;
    margin-left: .25rem;
}

.profile-form .input-div input,
.profile-form .input-div textarea {
    font: inherit;
}

form select {
    font-size: inherit;
}

form .phone-no,
form .location {
    margin-top: .3rem;
    border: 1px solid var(--brand-color);
    font-size: inherit;
    margin: 0;
    padding: .1rem .3rem;
    letter-spacing: .05rem;
}

textarea {
    font-family: inherit;
    font-size: inherit;
}

.bio:focus {
    box-shadow: none;
    border-bottom: 2px solid green;
}

label {
    font-size: 1.1em;
}

.edit-btn {
    padding: .2em;
    text-align: center;
}

#edit-btn {
    text-align: center;
    text-decoration: none;
    margin: .5rem auto;
    width: 75%;
}

#cancel-btn {
    background: var(--white-color)f;
    box-shadow: none;
    color: var(--brand-color);
}

.profile-btn {
    text-transform: capitalize;
    font-size: .95rem;
    border: none;
    border-radius: 1rem;
    padding: .5rem;
    background: var(--brand-color);
    color: var(--white-color);
    cursor: pointer;
    transition: .2s all ease-in;
    box-shadow: 0 0 4px 0 rgba(0, 0, 0, .25);
}

.profile-form-hide {
    display: none;
}

.u-total-views {
    background: linear-gradient(45deg, tomato, gold);
    padding: .3rem;
    color: var(--white-color);
    border-radius: .3rem;
}

.l-container .profile-bar {
    display: grid;
    grid-template-columns: 1fr;
    grid-gap: .3rem;
}
.profile-bar a, .profile-bar button{
    font: inherit;
    background: none;
    display: block;
    border: none;
    text-align: left;
    border-radius: 1rem;
    text-decoration: none;
    margin: 0;
    text-indent: .2rem;
    padding: .3rem .2rem;
    color: #068806;
    opacity: .5;
    text-transform: uppercase;
    font-weight: bold;
    border-left: 1px solid transparent;
    border-right: 1px solid transparent;
}
.profile-bar a:hover {
    background: var(--white-color);
    opacity: 1;
}

.profile-bar .profileNav-selected {
    background: var(--white-color);
    opacity: 1;
}

.profile-bar .logout-form {
    color: var(--red-color);
}

.user-info-hide {
    display: block;
}

.profile-help-card {
    display: none;
    position: absolute;
    top: 0;
    left: 0;
    left: 0;
    right: 0;
    z-index: 1;
    margin: 25% auto 0;
}

.profile-help-card .profile-help-div {
    position: relative;
    padding: 15px 0;
}

.profile-help-content {
    background-color: var(--white-color);
    margin: 0 1rem;
    border-radius: .5rem;
    padding: .5rem;
    text-indent: 1rem;
}

.profile-help-content::before {
    z-index: -1;
    content: '';
    box-shadow: 0 0 10px rgba(0, 0, 0, .5);
    margin: 11px;
    padding: 0rem;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: inherit;
    height: inherit;
    border-radius: .5rem;
    background: linear-gradient(45deg, tomato, gold);
}

.profile-help-content p {
    margin-top: .4rem;
    text-align: center;
}

.profile-help-img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
}

.help-btn {
    text-transform: capitalize;
    font-size: 1rem;
    border-radius: 1rem;
    border: 2px solid var(--brand-color);
    padding: .5em;
    background: var(--brand-color);
    color: var(--white-color);
    cursor: pointer;
    transition: .2s all ease-in;
    box-shadow: 0 0 4px rgba(1, 1, 1, .6);
}

.profile-hide {
    display: none;
}
</style>