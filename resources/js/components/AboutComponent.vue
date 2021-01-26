<template>
    <div class="" id="about-page">
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
                <div class="div-header">
                    <h3 class="div-meta">Kindly drop your suggestions on how we might improve.</h3>
                    <span class="hr-line"></span>
                </div>
            </div>
            <form action="" method="" @submit.prevent="sendFeedback">
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
    mounted(){
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
            $(e.target).closest(" button[type=submit]").html('<span class="loading-circle "></span>');
            await axios.post('action/feedback', this.feedback)
            .then(res=>{
                $(e.target).closest(" button[type=submit]").html('<i class="fas fa-paper-plane"></i> SEND FEEDBACK');
            })
            .catch(err=>console.log(err));
        }
    }
}
</script>