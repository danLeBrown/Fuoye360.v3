<template>
    <div>
        <header>
            <div class="h-wrap">
                <div class="h-block">
                    <div class="h-link">
                        <router-link :to="{name: 'home'}"><img loading="lazy" :src="asset('assets/images/360logo.png')" alt="logo"></router-link>
                    </div>
                    <div class="dynamic-div" v-if="$route.name != 'search'">
                        <div class="current-pg">
                            <h3 v-if="updateNav == 'home'"><i class="fas fa-home"></i> Home</h3>
                            <h3 v-else-if="updateNav == 'shop'"><i class="fas fa-shopping-cart"></i> Shop</h3>
                            <h3 v-else-if="updateNav == 'broadcast'"><i class="fas fa-bullhorn"></i> Broadcast</h3>
                            <h3 v-else-if="['search','visit'].includes(updateNav)"><i class="fas fa-search"></i> Search</h3>
                            <h3 v-else-if="updateNav == 'notifications'"><i class="fas fa-bell"></i> Notifications</h3>
                            <h3 v-else-if="updateNav == 'ad'"><i class="fas fa-ad"></i> Adverts</h3>
                        </div>
                        <div v-if="guest == true" class="profile-status">
                            <router-link :to="{name: 'login'}" class="login">Login <i class="fas fa-lock"></i></router-link>
                        </div>
                        <div v-else class="profile-status">
                            <div v-if="['shop', 'broadcast'].includes(updateNav)">
                                <button style="background: none; border:none" @click="toggleProfileState">
                                    <img loading="lazy" class="profile-display" :src="asset('storage/profile_images/'+user.image)"/>
                                </button>
                            </div>
                            <div v-else class="profile-status">
                                <a href="#" @click.prevent="logout" class="logout">EXIT <i class="fas fa-sign-out-alt"></i></a></div>
                        </div>                    
                    </div>
                    <div v-else class="dynamic-div">
                        <div class="search-div">
                            <form>
                                <div class="input-div">
                                    <input type="text" class="search-input" ><button class="search-btn"><i class="fas fa-search"></i> Search</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Side Nav -->
        <aside class="aside-nav">
            <div class="nav-block" style="z-index:2;">
                <div class="toggle">
                    <div class="nav-b">
                        <div class="nav-b-2">
                            <nav>
                                <div class="l-block" v-if="updateNav == 'home'">
                                    <router-link :to="{name : 'home'}" class="links link-1"><i class="fas fa-home"></i></router-link>
                                    <router-link :to="{name : 'shop'}" class="links link-2"><i class="fas fa-shopping-cart"></i></router-link>   
                                    <router-link :to="{name : 'broadcast'}" class="links link-3"><i class="fas fa-bullhorn"></i></router-link>
                                    <router-link :to="{name : 'search'}" class="links link-4 "><i class="fas fa-search"></i></router-link>
                                </div>
                                <div class="l-block" v-else-if="['search', 'visit'].includes(updateNav)">
                                    <router-link :to="{name : 'home'}" class="links link-2"><i class="fas fa-home"></i></router-link>
                                    <router-link :to="{name : 'shop'}" class="links link-3"><i class="fas fa-shopping-cart"></i></router-link>   
                                    <router-link :to="{name : 'broadcast'}" class="links link-4"><i class="fas fa-bullhorn"></i></router-link>
                                    <router-link :to="{name : 'search'}" class="links link-1 router-link-exact-active active-l"><i class="fas fa-search"></i></router-link>
                                </div>
                                <div class="l-block" v-else-if="updateNav == 'shop'">
                                    <router-link :to="{name : 'home'}" class="links link-4 "><i class="fas fa-home"></i></router-link>
                                    <router-link :to="{name : 'shop'}" class="links link-1 router-link-exact-active active-l"><i class="fas fa-shopping-cart"></i></router-link>   
                                    <router-link :to="{name : 'broadcast'}" class="links link-2 "><i class="fas fa-bullhorn"></i></router-link>
                                    <router-link :to="{name : 'search'}" class="links link-3 "><i class="fas fa-search"></i></router-link>
                                </div>
                                <div class="l-block" v-else-if="updateNav == 'broadcast'">
                                    <router-link :to="{name : 'home'}" class="links link-3 "><i class="fas fa-home"></i></router-link>
                                    <router-link :to="{name : 'shop'}" class="links link-4 "><i class="fas fa-shopping-cart"></i></router-link>   
                                    <router-link :to="{name : 'broadcast'}" class="links link-1 router-link-exact-active active-l"><i class="fas fa-bullhorn"></i></router-link>
                                    <router-link :to="{name : 'search'}" class="links link-2 "><i class="fas fa-search"></i></router-link>
                                </div>
                                <div class="l-block" v-else>
                                    <router-link :to="{name : 'home'}" class="links link-1"><i class="fas fa-home"></i></router-link>
                                    <router-link :to="{name : 'shop'}" class="links link-2"><i class="fas fa-shopping-cart"></i></router-link>   
                                    <router-link :to="{name : 'broadcast'}" class="links link-3"><i class="fas fa-bullhorn"></i></router-link>
                                    <router-link :to="{name : 'search'}" class="links link-4 "><i class="fas fa-search"></i></router-link>
                                </div>
                            </nav>
                        </div>        
                    </div>
                    <div class="t-block">
                        <span class="s-1"></span>
                        <span class="s-2"></span>
                        <span class="s-3"></span>
                    </div>
                </div>
            </div>
            <div class="fast-link-container">
                <router-link v-if="this.guest == false" :to="{name: 'notifications'}" class="fast-router router-notification-div"><i class="fas fa-bell"></i></router-link>
                <button class="fast-router router-broadcast-div" v-if="$route.name == 'broadcast' && this.guest == false" @click="newBroadcast"><i class="fas fa-bullhorn"></i></button>
                <router-link v-if="$route.name == 'shop'" :to="{name: 'shop', params : {page: 'cart'}}" class="fast-router router-shop-div"><i class="fas fa-shopping-cart"></i></router-link>
            </div>
        </aside>
    </div>
</template>

<script>
export default {
    name: 'navbar',
    props: ["updateNav", "guest", "user", "profileState"],
    data(){
        return{
            navStatus:[],
            currentPage:'',
            // search:{},
            // guest: true
            st: 0,
            lastScrollTop: 0
        }
    },
    mounted() {
        let status = 0
        window.addEventListener("scroll", function(e) {
            this.st = $(this).scrollTop();
            if(this.st > this.lastScrollTop){
                $("aside").hide()
                status = 1;
            }else{
                // up
                $("aside").show()
                // $("aside, .fast-link-container").show();
            }
            this.lastScrollTop = this.st;
        });

        let btnStatus = false;
        let ts;

        $('.t-block').click(function(e) {
            e.preventDefault();
            let deg = 0;
            let degg = 0;
            if (btnStatus == false) {
                document.querySelector('.nav-block .s-1').style.transform = "rotate(35deg) translateY(4px) translateX(3px)";
                document.querySelector('.nav-block .s-1').style.width = "35px";
                document.querySelector('.nav-block .s-2').style.transform = "scale(0)";
                document.querySelector('.nav-block .s-3').style.width = "35px";
                document.querySelector('.nav-block .s-3').style.transform = "rotate(-35deg) translateY(-4px) translateX(3px)";

                btnStatus = true;

                $(document).on("keydown", function(e) {
                    setTimeout(() => {
                        if (e.keyCode == '39') {
                            deg += 90;
                            degg -= 90
                                //right arrow key
                            $('.nav-block nav').css('transform', 'rotate(' + deg + 'deg)');
                            $('.nav-block .toggle nav .l-block .links').css('transform', 'rotate(' + degg + 'deg)');
                        } else if (e.keyCode == '37') {
                            deg -= 90;
                            degg += 90;
                            //left arrow key
                            $('.nav-block nav').css('transform', 'rotate(' + deg + 'deg)');
                            $('.nav-block .toggle nav .l-block .links').css('transform', 'rotate(' + degg + 'deg)');
                        }
                    }, 500);
                    if (e.keyCode == '27') {
                        closeAsideNav();
                        $('.nav-block .nav-b').fadeOut();
                        $('.nav-block nav').css('transform', 'rotate(0deg');
                        $('.nav-block .toggle nav .l-block .links').css('transform', 'rotate(0deg)');
                    }
                });
                // rotate();

                $(document).on('swiped-left', function() {
                    deg -= 90;
                    degg += 90;
                    $('.nav-block nav').css('transform', 'rotate(' + deg + 'deg)');
                    $('.nav-block .toggle nav .l-block .links').css('transform', 'rotate(' + degg + 'deg)');
                });
                $(document).on('swiped-right', function() {
                    deg += 90;
                    degg -= 90;
                    $('.nav-block nav').css('transform', 'rotate(' + deg + 'deg)');
                    $('.nav-block .toggle nav .l-block .links').css('transform', 'rotate(' + degg + 'deg)');
                });
                $('.nav-block nav').css('transform', 'rotate(0deg');
                $('.nav-block .toggle nav .l-block .links').css('transform', 'rotate(0deg)');

            } else {
                closeAsideNav();
            }
            $('.nav-block .nav-b').fadeToggle();
            $('.nav-block nav').css('transform', 'rotate(0deg');
            $('.nav-block .toggle nav .l-block .links').css('transform', 'rotate(0deg)');

        });
        function closeAsideNav() {
            document.querySelector('.nav-block .s-1').style.transform = "rotate(0deg)";
            document.querySelector('.nav-block .s-1').style.width = "35px";
            document.querySelector('.nav-block .s-2').style.transform = "scale(1)";
            document.querySelector('.nav-block .s-3').style.width = "25px";
            document.querySelector('.nav-block .s-3').style.transform = "rotate(0deg)";
            btnStatus = false;
        }
            
    },
    methods:{ 
        resetNavStatus(page){
            // console.log(page);
            this.navStatus.forEach(element => {
                element = false;
                console.log(element);
            });
            if (page == 'home') {    
                this.navStatus['home'] = true;
                this.currentPage = '<i class="fas fa-home"></i> Home';
            }else if (page == 'search') {
                this.navStatus['search'] = true;
                this.currentPage = '<i class="fas fa-search"></i> search';
            }else if (page == 'shop') {            
                this.navStatus['shop'] = true;
                this.currentPage = '<i class="fas fa-shopping-cart"></i> Shop';
            }else if (page == 'broadcast') {
                this.navStatus['broadcast'] = true;
                this.currentPage = '<i class="fas fa-bullhorn"></i> Broadcast';
            }else {
                // this.navStatus['home'] = true;
                // this.currentPage = '<i class="fas fa-home"></i> Home';
            }
        },
        logout(){
            this.$emit('logout');
        },
        toggleProfileState(){
            if (this.profileState == false) {
                this.$emit('toggleProfile', true);
            }else{
                this.$emit('toggleProfile', false);
            }
        },
        newBroadcast(){
            this.$emit('newBroadcast');
        }
    }
}
</script>
<style scoped>
.search-input{
    outline: none;
    padding: 0.5rem;
    font: inherit;
    border-radius: 0.5rem 0px 0px 0.5rem;
    border: none;
}
.search-btn{
    padding: .5rem;
    font: inherit;
    border: none;
    border-radius: 0 .5rem .5rem 0;
    background: var(--brand-color);
    color: var(--white-color);
}
</style>