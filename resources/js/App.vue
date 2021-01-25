<template>
  <div id="app" :class="{'auth-form-display': authForm}">
    <alert v-bind:alerts="errors"></alert>
    <navbar-component v-bind:updateNav="$route.name" v-bind:guest="guest" v-bind:user="user" v-on:toggleProfile="toggleProfileState" v-on:logout="logout" v-on:newBroadcast="newBroadcast" v-bind:profileState="profileState"></navbar-component>
    <div class="container">
      <main class="main-wrapper">
        <div class="back-btn-div">
          <button type="button" @click="goBack"><i class="fas fa-arrow-left"></i></button>
        </div>
        <profile-component v-if="!guest" v-bind:user="user" v-bind:profileState="profileState" v-on:logout="logout" v-on:newBroadcast="newBroadcast" v-on:updateProfile="updateProfile" v-on:toggleProfile="toggleProfileState"></profile-component>
        <transition :name="transitionName">
          <router-view v-on:alertNotification="alertNotification" v-on:authentication="authenticate" v-bind:user="user" v-on:updateUser="updateUser" v-bind:createBroadcast="createBroadcast" v-on:newBroadcast="newBroadcast" v-on:closeBroadcast="closeBroadcast" v-on:viewImage="viewImage"/>
        </transition>
      </main>
    </div>
    <div class="vs-wrapper" id="logout-form" v-if="showLogout" @click="checkToLogout($event)">
      <div class="vs-container">
        <div class="vs-content">
          <a href="#l-div" class="share-scroll-down"></a><br>
          <h3 style="text-align:center;">You're about to exit FUOYE360?</h3>          
          <form action="#">
            <button type="button" class="no-logout" @click="closeLogout">NO</button>
            <button type="button" class="yes-logout" @click="affirmLogout">YES</button>
          </form>
        </div>
      </div>
    </div>

    <div class="vs-wrapper" id="view-image" @click="closeImage($event)">
      <div class="vs-container">
        <div class="vs-content">
          <img loading="lazy" src="" alt="" class="">
        </div>
      </div>
    </div>
    <div class="vs-wrapper" id="preloader">
      <div class="vs-container">
        <div class="vs-content">
            <svg version="1.2" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 612 792" style="enable-background:new 0 0 612 792;" xml:space="preserve">
              <path class="st2" d="M264.3,460.1c6.9-12.1,17-18.3,30.8-15.2c13.7,3.1,20.4,12.9,21.4,26.7c29.9,1.8,70.6-43.6,65.1-72.6
              c-13.6,0.4-24-4.9-28.7-18.2c-4.8-13.6,0.4-24.3,11.5-32.5c-23-30-67.2-35.3-94.9-18.3c7.1,11.2,8,22.7-0.5,33.2
              c-9.2,11.5-21.2,13-34.4,7.3C223.1,398.2,231.8,439.2,264.3,460.1z M305.4,477.7c0-10.4-8.3-18.5-18.7-18.5
              c-10.1,0-18.5,8.4-18.5,18.6c-0.1,10,8.6,18.8,18.7,18.7C297.1,496.5,305.4,488.1,305.4,477.7z M383.5,349.8
              c-10.2-0.1-18.8,8.1-18.9,18.1c-0.2,10.5,8.1,19.1,18.5,19.2c10.2,0.1,18.8-8.4,18.8-18.6C401.9,358.4,393.5,349.9,383.5,349.8z
              M223.9,342.2c0,10.5,8.1,18.5,18.6,18.4c10.5-0.1,18.4-8.1,18.3-18.7c-0.1-10.3-8.1-18.3-18.5-18.3
              C231.9,323.7,223.9,331.7,223.9,342.2z">
              <animateTransform id="rotate" attributeName="transform" type="rotate" from="0 306 396 " to="360 306 396" begin="0s;rotate.end" dur="1.7s"/>
            </path>
            <g>
              <path class="st0" d="M334.5,413c5.9,0,10.1-2.8,12.2-8.4c2.5-6.7,2.5-13.6,0.1-20.4c-2-5.6-6.3-8.6-12.1-8.6
              c-5.6,0-10.1,2.8-12.1,8.2c-2.7,7.1-2.7,14.4,0.3,21.5C324.9,410.4,328.9,413,334.5,413z"/>
              <path class="st0" d="M311.5,377.2c-0.1-0.2-0.3-0.5-0.4-0.7c-2.9,0-5.8-0.1-8.7,0.1c-0.6,0-1.4,0.6-1.7,1.1
              c-3.2,4.7-6.4,9.5-9.4,14.3c-2.2,3.5-2.8,7.5-1.9,11.5c1.2,5.1,5.6,8.7,11.3,9.4c6.1,0.8,11.9-1.7,14.6-6.4
              c3.9-6.8,0.8-15.3-6.5-17.6c-1.6-0.5-3.2-0.5-5-0.8C306.4,384.5,308.9,380.8,311.5,377.2z"/>
              <path class="st0" d="M278.4,393.5c3.9-2.1,5.6-5.1,5-9.2c-0.7-4.2-3.5-6.7-7.4-7.9c-3.8-1.1-7.6-1.1-11.2,0.6
              c-3.4,1.5-5.9,4-6.9,8.1c2.5,0.6,4.9,1.4,7.2,1.5c0.8,0,1.5-2.1,2.6-2.4c1.6-0.5,3.4-0.8,5-0.4c1.8,0.4,2,2.2,1.6,3.9
              c-0.4,1.9-1.9,2.5-3.6,2.7c-1.4,0.2-2.9,0.4-4.7,0.6c0,1.3,0.1,2.6,0,3.9c-0.2,1.8,0.5,2.5,2.3,2.5c1.6,0,3.3,0.3,4.8,0.8
              c1.8,0.7,2.5,2.3,2.1,4.2c-0.4,1.8-1.6,2.8-3.4,3.1c-2.7,0.4-5-0.3-6.5-2.8c-0.3-0.5-1.2-1.1-1.7-1c-2.3,0.5-4.5,1.1-6.7,1.7
              c0.6,3.1,2,5.4,4.4,7c5.4,3.4,11,3.7,16.8,1.1c3.9-1.8,6-5.1,6.1-9.3C284.5,398.1,282.4,395.3,278.4,393.5z"/>
              <circle class="st2" cx="302.9" cy="400.4" r="5.6"/>
              <ellipse class="st2" cx="334.5" cy="394.8" rx="4.5" ry="10.8"/>
            </g>
          </svg>
        </div>
      </div>
    </div>
  </div>
</template>
<style scoped>
#preloader{
  display: none;
}
  #preloader .vs-container{
    margin: 0 auto;
    height: calc(100vh - 68px);
  }
  #preloader .vs-content{
    background: none;
  }
  .st0{fill:#FFFFFF;}
  .st1{fill:none;}
  /* .st2{fill:#00A959;} */
  .st2{fill:var(--brand-color);}

  #logout-form .vs-container{
    height: 100%;
    margin-top: 0;
    align-items: flex-end;
  }
  #logout-form form{
    margin-top: 1rem;
    display: flex;
    padding: 1rem;
    border-radius:1rem;
  }

  #logout-form button{
    width: 100%;
    padding: .5rem;
    border-radius: .5rem;
    margin: .5rem;
  }

  .no-logout{
    border: none;
    background: var(--red-color);
    color: var(--white-color);
  }

  .yes-logout{
    background: none;
    border: 2px solid var(--red-color);
    color: var(--red-color);
  }
  .back-btn-div{
    position: fixed;
    width: 50px;
    height: 50px;
    z-index: 10;
    bottom: 15px;
    left: 15px;
  }
  .back-btn-div button{
    border-radius: 50%;
    box-shadow: 0 0 4px 0 rgba(0,0,0, .15);
    border: none;
    width: inherit;
    height: inherit;
    background: var(--white-color);
    color: var(--brand-color);
    font-size: inherit;
  }
  #view-image{
    display: none;
  }
  
  #view-image .vs-content{
    background: none;
  }
  #view-image img{
    width: 100%;
    height: 50%;
    border-radius: 1rem;
  }
</style>
<script>
import Alert from './components/Alert.vue';
import NavbarComponent from './components/NavbarComponent.vue'
import ProfileComponent from './components/ProfileComponent.vue';
export default {
    name : "app",
    data() {
      return {
        authForm:false,
        guest: true,
        errors:[],
        user:{},
        profileState: false,
        showLogout: false,
        createBroadcast: false,
        transitionName: 'slide-left'
      }
    },
    components:{
      NavbarComponent,
      Alert,
      ProfileComponent
    },
    watch:{
      $route(to, from){
        if(['login', 'register'].includes(this.$route.name)){
          this.authForm = true;
        }else{
          this.authForm = false;
        }
        $("#preloader").fadeIn();
        setTimeout(()=>{
          $("#preloader").fadeOut();
        },1500);
        this.profileState = false;       
        const toDepth = to.path.split('/').length;
        const fromDepth = from.path.split('/').length;
        this.transitionName = toDepth < fromDepth ? 'slide-right' : 'slide-left';
      }
    },
    mounted() {
      $("#logout-form").click(function(e) {
        if ($(e.target).closest(".vs-content").length === 0) {
            $(this).fadeOut();
        }
    });
    },
    created(){
      axios.get('/api/user')
      .then((res)=>{
        this.user = res.data;
        this.guest = false;
      })
      .catch((err)=>console.log(err));
    },
    methods:{
      alertNotification(alert){
        this.errors = alert;
      },
      authenticate(){
        axios.get('/sanctum/csrf-cookie').then(response => {
          axios.get('/api/user')
          .then((res)=>{
            this.user = res.data;
            this.guest = false;
          })
          .catch((err)=>console.log(err));
        });
      },
      toggleProfileState(data){
        this.profileState = data;
      },
      updateUser(data){
        this.user.following = data.user.following;
      },
      logout(){
        this.showLogout = true;
      },
      closeLogout(){
        this.showLogout = false;
      },
      affirmLogout(){
        axios.get('/sanctum/csrf-cookie').then(response => {
          axios.post('/api/logout')
          .then((res) => {
            this.$router.push({name: 'home'});
            this.showLogout = false;
            this.guest = true;
          })
          .catch((err) => { console.log(err)});
        });
      },
      newBroadcast(){
        this.createBroadcast = true;
      },
      closeBroadcast(){
        this.createBroadcast = false;
      },
      goBack(){
        // console.log(history);
        history.back();
      },
      updateProfile(){
        axios.get('/api/user')
        .then((res)=>{
            this.user = res.data;
        })
        .catch((err)=>console.log(err));
      },
      checkToLogout(e){
        if ($(e.target).closest(".vs-content").length === 0) {
          this.closeLogout();
        }
      },
      viewImage(src){
        $("#view-image img").attr("src", src);
        $("#view-image").fadeToggle();
      },
      closeImage(e){
        if ($(e.target).closest("img").length === 0) {
          $("#view-image").fadeToggle();
        }
      }
    }
  }
</script>
