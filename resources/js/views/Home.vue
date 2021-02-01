<template>
    <div class="" id="about-page">
        <div class="main-txt wrapper"><br>
            <div class="div-header">
                <h1 style="white-space:normal;line-height: 5rem;font-size:50px;text-align:left;"><span class="typewrite"></span>.</h1>
            </div>
            <div class="intro-div">
                <p><span class="t_success">FUOYE360</span> is your favourite <span class="t_success">problem SOLVING community</span> created to provide solutions to some of the prevailing Educational, Social, Entrepreneurship challenges we face in our everyday lives.</p>
            </div>
        </div>
        <!-- <div class="how-it-works wrapper">
            <div class="div-header" style="color:var(--brand-color)"><br>
                <h2>How <span>FUOYE360</span> Works</h2>
            </div><br>
            <div class="div-content">
                <div class="i-rows" data-aos="fade-up">
                    <div class="fig-div">
                        <span class="lg-fig">1</span>
                    </div>
                    <div class="text-div">
                        <h3 class="std-out-two"><i class="fas fa-lock"></i> Create An Account</h3>
                        <p>
                            <router-link :to="{name:'register'}" style="color: var(--dark-color);">Creating an account</router-link> allows you to Buy, Sell and do other exciting things on FUOYE360
                        </p>
                    </div>
                </div>
                <div class="i-rows" data-aos="fade-up" >
                    <div class="fig-div">
                        <span class="lg-fig">2</span>
                    </div>
                    <div class="text-div">
                        <h3 class="std-out-two"><i class="fas fa-user"></i> Update Your Profile</h3>
                        <p>
                            Update your profile image, location and use a catchy bio to make your account stand out
                        </p>
                    </div>
                </div>
                <div class="i-rows" data-aos="fade-up">
                    <div class="fig-div">
                        <span class="lg-fig">3</span>
                    </div>
                    <div class="text-div">
                        <h3 class="std-out-two"><i class="fas fa-search"></i> Browse The Shops</h3>
                        <p>
                            Browse the shops to find awesome products to buy, favourite vendors to follow <i class="fas fa-plus"></i> and add products you hope to buy later to your <i class="fas fa-bookmark"></i> wishlist
                        </p>
                    </div>
                </div>
                <div class="i-rows" data-aos="fade-up">
                    <div class="fig-div">
                        <span class="lg-fig">4</span>
                    </div>
                    <div class="text-div">
                        <h3 class="std-out-two"><i class="fas fa-shopping-cart"></i> Add Product To Cart</h3>
                        <p>
                            Add Products <i class="fas fa-shopping-bag"></i> you want to buy now to your Cart. And proceed to Cart to check them out.
                        </p>
                    </div>
                </div>
                <div class="i-rows" data-aos="fade-up">
                    <div class="fig-div">
                        <span class="lg-fig">5</span>
                    </div>
                    <div class="text-div">
                        <h3 class="std-out-two"><i class="fas fa-phone fa-flip-horizontal"></i> Contact Seller</h3>
                        <p>
                            Contact sellers on their WhatsApp account <i class="fab fa-whatsapp"></i> to let them know what want you want to buy from them. Meet these awesome sellers on or off campus to complete purchase. 
                        </p>
                    </div>
                </div>
            </div>
        </div> -->
        <!-- <div class="gallery">
            <div class="div-header">
                <h2>Our <span class="std-out">Gallery</span></h2>
            </div>
            <div class="div-content">
                <img loading="lazy" :src="asset('assets/images/1.jpg')" alt="" srcset="" data-aos="fade-up">
                <img loading="lazy" :src="asset('assets/images/2.jpg')" alt="" srcset="" data-aos="fade-up">
                <img loading="lazy" :src="asset('assets/images/3.jpg')" alt="" srcset="" data-aos="fade-up">
                <img loading="lazy" :src="asset('assets/images/fuoye_360.jpg')" alt="" srcset="" data-aos="fade-up">
                <img loading="lazy" :src="asset('assets/images/1.png')" alt="" srcset="" data-aos="fade-up">
                <img loading="lazy" :src="asset('assets/images/2.png')" alt="" srcset="" data-aos="fade-up">
                <img loading="lazy" :src="asset('assets/images/3.png')" alt="" srcset="" data-aos="fade-up">
                <img loading="lazy" :src="asset('assets/images/4.png')" alt="" srcset="" data-aos="fade-up">
                <img loading="lazy" :src="asset('assets/images/5.png')" alt="" srcset="" data-aos="fade-up">
                <img loading="lazy" :src="asset('assets/images/6.png')" alt="" srcset="" data-aos="fade-up">
                <img loading="lazy" :src="asset('assets/images/7.png')" alt="" srcset="" data-aos="fade-up">
                <img loading="lazy" :src="asset('assets/images/8.png')" alt="" srcset="" data-aos="fade-up">
            </div>
        </div> -->
        <div class="wrapper">
            <h3 class="sb-text">
                <i class="fas fa-user"></i> Our awesome Developer!
            </h3><br>
            <div class="about-inspiration">
                <div class="about-avi">
                    <img loading="lazy" :src="asset('assets/images/about.jpg')" alt="about-image" style="margin: 0 auto;"/> 
                </div>
                <div>
                    <div class="goals">
                    <span class="content">Words That Inspire Me:</span><br>"Think Differently. <br>Innovate Differently. <br> Provide Solutions rather than discuss problems. <br> And while you do these things, remember than your greatest strength lies in doing things in your own uniqueness. <br>THESE ARE THE WORDS I LIVE AND BREATHE BY"
                    </div>
                    <p>Ayomide Daniel is a 400 Level Mechanical Engineering student of FUOYE. FUOYE360 is his first website to be launched.</p>
                </div>
            </div>
        </div>
        <div class="form-container lg-container" style="background:none;">
            <div style="padding:1rem;" class="form-img-div">
                <img loading="lazy" :src="asset('assets/illustrations/messenger-animate.svg')" alt="">
                <div class="div-header" style="text-align:left;">
                    <h3 class="div-meta">Kindly drop your suggestions on how we might improve.</h3>
                    <span class="hr-line"></span>
                </div>
            </div>
            <form action="" method="" @submit.prevent="sendFeedback($event)">
                <div class="input-div">
                    <label for="text"><i class="fas fa-user"></i> Name</label>
                    <input type="text" name="username" id="feedbackName" value="" @input="validateFormName($event)" v-model="feedback.name">
                    <div class="form-validation"></div>
                </div>
                <input type="hidden" name="redirect" value="shop-link"/>
                <div class="input-div">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" name="email" value="" @input="validateForm($event)" id="feedbackEmail" v-model="feedback.email">
                    <div class="form-validation"></div>
                </div>
                <div class="input-div">
                    <label for="feedback"><i class="fas fa-question-circle"></i> Feedback</label>
                    <textarea name="feedback" id="feedbackMsg" rows="10" v-model="feedback.message"></textarea>
                </div>
                <button type="submit" name="signup-btn" class="send-feeback-btn" :disabled="feedback.name.length <= 0 || feedback.email.length <= 0 || feedback.message.length <= 0|| !valid_email" :style="feedback.name.length <= 0 || feedback.email.length <= 0 || feedback.message.length <= 0 || !valid_email? 'opacity:.75;': ''"><i class="fas fa-paper-plane"></i> SEND FEEDBACK</button>
            </form>
        </div>
  </div>
</template>
<script>
export default {
    data() {
        return {
            feedback: {
                name: '',
                email: '',
                message: ''
            },
            valid_email: false,
        }
    },
    created() {
        
    },
    mounted(){
      $(document).ready(()=>{
          $('.form-container input, .form-container textarea').on('focusin', function() {
              $(this).parent().find('label').addClass('active');
          });
          $('.form-container input, .form-container textarea').on('focusout', function() {
              if (!this.value) {
                  $(this).parent().find('label').removeClass('active');
              }
          });
          $('.form-container label').click(function() {
              $(this).parent().find('input').focus();
          });
          let elements = document.getElementsByClassName('typewrite');
          for (let i = 0; i < elements.length; i++) {
              let toRotate = ["Entering the University is <span class=\"std-out\">HARD</span>", "Being a student should be <span class=\"std-out\">EASY</span>", "Welcome to <span class=\"std-out\">FUOYE360</span>"];
              let period = 2000;
              if (toRotate) {
                  new TxtType(elements[i], toRotate, period);
              }
          }
  
        });
          let TxtType = function(el, toRotate, period) {
            this.toRotate = toRotate;
            this.el = el;
            this.loopNum = 0;
            this.period = parseInt(period, 10) || 2000;
            this.txt = '';
            this.tick();
            this.isDeleting = false;
        };

        TxtType.prototype.tick = function() {
            let i = this.loopNum % this.toRotate.length;
            let fullTxt = this.toRotate[i];

            if (this.isDeleting) {
                this.txt = fullTxt.substring(0, this.txt.length - 1);
            } else {
                this.txt = fullTxt.substring(0, this.txt.length + 1);
            }

            this.el.innerHTML = this.txt;

            var that = this;
            let delta = 200 - Math.random() * 100;

            if (this.isDeleting) {
                delta /= 2;
            }

            if (!this.isDeleting && this.txt === fullTxt) {
                delta = this.period;
                this.isDeleting = true;
            } else if (this.isDeleting && this.txt === '') {
                this.isDeleting = false;
                this.loopNum++;
                delta = 500;
            }

            setTimeout(function() {
                that.tick();
            }, delta);
        };
    },
    methods: {
        validateFormName(e) {
            let formInput, format;
            format = /[`!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/;
            if (format.test(e.target.value)) {
                $(e.target.parentElement).children('.form-validation').removeClass('i-invalid');
                $(e.target.parentElement).children('.form-validation').removeClass('valid');
                $(e.target.parentElement).children('.form-validation').addClass('invalid').html("&times;").show();
            } else if (e.target.value == '') {
                $(e.target.parentElement).children('.form-validation').removeClass('valid').addClass('invalid').html("<i class=\"fas fa-info-circle\"></i>").show();
            } else {
                $(e.target.parentElement).children('.form-validation').removeClass('invalid');
                $(e.target.parentElement).children('.form-validation').addClass('valid').html("&check;").show();
            }
        },
        validateForm(e){
            let formInput,format;
            formInput = e.target.name;
            if (formInput == "email") {
                format = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                if (format.test(e.target.value)) {
                    $(e.target.parentElement).children('.form-validation').removeClass('invalid');
                    $(e.target.parentElement).children('.form-validation').removeClass('i-invalid');
                    $(e.target.parentElement).children('.form-validation').addClass('valid').html("&check;").show();
                    this.valid_email = true;
                } else if (e.target.value == '') {
                    $(e.target.parentElement).children('.form-validation').removeClass('valid').addClass('invalid').html("<i class=\"fas fa-info-circle\"></i>").show();
                    this.valid_email = false;
                } else {
                    $(e.target.parentElement).children('.form-validation').removeClass('i-invalid');
                    $(e.target.parentElement).children('.form-validation').removeClass('valid');
                    $(e.target.parentElement).children('.form-validation').addClass('invalid').html("&times;").show();
                    this.valid_email = false;
                }
            } 
        },
        async sendFeedback(e){
            await $("button[type=submit]").html('<span class="loading-circle "></span>');
            await axios.post('/api/action/feedback', this.feedback)
            .then(res=>{
              this.feedback.name = '';
              this.feedback.email = '';
              this.feedback.message = '';
                $("button[type=submit]").html('<i class="fas fa-paper-plane"></i> SEND FEEDBACK');
            })
            .catch(err=>console.log(err));
        }
    }
}
</script>
<style scoped>
    .std-out{
        color: #fff;
        padding: .5rem;
        border-radius: .5rem;
        background: #ffa500;
    }
    .std-out-two{
        background: #32cd32;
        color: #fff;
        padding: .5rem;
        border-radius: .5rem;
    }
    .div-header{
        white-space:normal;
        line-height: 2rem;
        text-align: center;
    }
    .main-txt{
        margin-top: -.3rem;
        height: 75vh;
        padding: 1rem;
        color: #32cd32;
        position: relative;
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
    }
    .typewrite{
        transition: all ease-in-out 3s;
    }
    .intro-div{
        position: absolute;
        top: 45%;
        left: 0%;
        right: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        background: #fff;
        color: #32cd32;
        border-radius: 1rem;
        font-weight: bold;
        padding: 1.5rem; 
        margin: 0 auto;
        width: 85%;
        line-height: 2rem;
        max-width: 425px;
        display: none;
    }
    .how-it-works{
        min-height: 50vh;
        background: var(--white-color);
        color: var(--dark-color);
        text-align: center;
        position: relative;
    }
    /* .how-it-works::after{
        display: block;
        content: '';
        position: absolute;
        z-index: -1;
        width: inherit;
        height: 200px;
        background: url("asset('assets/images/how-it-works.svg')}}");
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center;
        left: 0;
        right: 0;
        bottom: -150px;
    } */
    .lg-fig{
        font-size: 150px;
        font-weight: bold;
        font-family: 'Ubuntu', sans-serif;
        /* color: rgba(255, 217, 0, 0.87); */
        color: var(--brand-color);
        color: var(--highlight-color);
    }
    .i-rows{
        display: flex;
        justify-content: stretch;
        align-items: center;
        min-height: 150px;
        margin: 2rem 0;
    }
    .i-rows .fig-div{
        height: inherit; 
    }
    .i-rows .text-div{
        align-self: flex-start;
    }
    .gallery{
        text-align: center;
    }
    .gallery .div-content{
        padding: 1rem;
        display: grid;
        grid-template-columns: repeat(2,1fr);
        grid-gap: .5rem;
    }
    .gallery .div-content img{
        width: 100%;
        height: auto;
        border-radius: 1rem;
        margin: .25rem;
    }
    @media screen and (min-width: 768px){
        .how-it-works .div-content{
            display: grid;
            grid-template-columns: repeat(2,1fr);
            grid-gap: .5rem;
        }
        .gallery .div-content{
            display: grid;
            grid-template-columns: repeat(4,1fr);
        }
        .how-it-works::after{
            display: none;
            width: inherit;
            min-height: 50vh;
            bottom: -250px;
        }
        p{
            white-space: normal;
        }
    }
</style>