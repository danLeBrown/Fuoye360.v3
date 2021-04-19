<template>
    <div class="wrapper">
        <div class="return-home">
            <router-link :to="{name: 'home'}"><i class="fas fa-home"></i> GO BACK HOME</router-link>
        </div>
        <br><br>
        <div class="form-container lg-container" style="background:none;">
            <div style="padding:1rem;" class="form-img-div">
                <img loading="lazy" :src="asset('assets/illustrations/group-chat-animate.svg')" alt="">
                <div class="div-header">
                    <h3 class="div-meta">New to FUOYE360 uhn? Create an account to connect with other awesome users</h3>
                    <span class="hr-line"></span>
                </div>
            </div>
            <form>
                <div class="input-div"
                >
                    <label for="uName"><i class="fas fa-user"></i> Username</label>
                    <input type="text" name="username" id="uName" value="" @input="validateForm($event)" v-model="form.username" required>
                    <div class="form-validation">
                    </div>
                </div>
                <div class="input-div">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input required type="email" name="email" value="" autocomplete="email" v-on:input="validateForm($event)" v-model="form.email">
                    <div class="form-validation">
                    </div>
                </div>
                <div class="input-div">
                    <label for="mobile_number"><i class="fas fa-phone-alt"></i> Mobile number</label>
                    <input required type="text" name="mobile_number" maxlength="11" value="" v-on:input="validateForm($event)" v-model="form.mobile_number">
                    <div class="form-validation">
                    </div>
                </div>
                <div class="input-div">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <input required :type="!showPassword? 'password': 'text'" name="password" autocomplete="new-password" id="password" v-model="form.password">
                    <button type="button" id="show-password" @click="showPassword = !showPassword"><span v-if="!showPassword"><i class="fas fa-eye"></i></span><span v-else><i class="fas fa-eye-slash"></i></span></button>
                    <div class="form-validation">
                    </div>
                    <small v-if="form.password.length > 1 && form.password.length < 8" style="position:absolute;bottom:-20px;color:red;">*Password must be more than 8 characters</small>
                </div>
                <button type="submit" name="signup-btn" style="margin-top:.25rem;" @click.prevent="register">Signup</button>
                <div>
                    <p>By signing up, you agree to our <a href="/terms" target="_blank">terms and conditons</a></p>
                    <router-link :to="{name: 'login', query:{redirect: $route.query.redirect}}">Already have an account?</router-link>
                </div>
            </form>
        </div>
    </div>
</template>
<script>
export default {
    data(){
        return{
            form:{
                username:"",
                email:"",
                password:"",
                mobile_number:"",
                device_name: "fuoye360_webapp"
            },
            errors :[],
            showPassword: false
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
        $("#uName").keyup(function() {
            this.value = this.value.replace(/ /g, "_")
        });
        $('.form-container label').click(function() {
            $(this).parent().find('input').focus();
        });
    },
    created(){
        this.currentPage = '<i class="fas fa-lock"> Login';
    },

    methods: {
        validateForm(e){
            let formInput,format;
            formInput = e.target.name;
            if (formInput == 'username') {
                format = /[ `!@#$%^&*()+\-=\[\]{};':"\\|,.<>\/?~]/;
                if (e.target.value.substr(-1) == "_" || e.target.value.substr(-1) == " " || e.target.value.substr(1) == " " || e.target.value.substr(0) == "_" || format.test(e.target.value)) {
                    $(e.target.parentElement).children('.form-validation').removeClass('i-invalid');
                    $(e.target.parentElement).children('.form-validation').removeClass('valid');
                    $(e.target.parentElement).children('.form-validation').addClass('invalid').html("&times;").show();
                } else if (e.target.value == '') {
                    $(e.target.parentElement).children('.form-validation').removeClass('valid').addClass('invalid i-invalid').html("<i class=\"fas fa-info-circle\"></i>").show();
                } else {
                    $(e.target.parentElement).children('.form-validation').removeClass('invalid');
                    $(e.target.parentElement).children('.form-validation').removeClass('i-invalid');
                    $(e.target.parentElement).children('.form-validation').addClass('valid').html("&check;").show();
                }
            } else if (formInput == "mobile_number") {
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
            } else if (formInput == "email") {
                format = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                if (format.test(e.target.value)) {
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
            } else if (formInput == "location") {
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
            } else if (formInput == "password") {
                console.log(e.target.value);
                if (e.target.value.length < 8) {
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
            } else if (formInput == "password_confirmation") {
                if (e.target.value == $("#password").val()) {
                    $(e.target.parentElement).children('.form-validation').removeClass('invalid');
                    $(e.target.parentElement).children('.form-validation').removeClass('i-invalid');
                    $(e.target.parentElement).children('.form-validation').addClass('valid').html("&check;").show();
                } else if (e.target.value == '') {
                    $(e.target.parentElement).children('.form-validation').addClass('invalid').html("<i class=\"fas fa-info-circle\"></i>").show();
                } else {
                    $(e.target.parentElement).children('.form-validation').removeClass('i-invalid');
                    $(e.target.parentElement).children('.form-validation').removeClass('valid');
                    $(e.target.parentElement).children('.form-validation').addClass('invalid').html("&times;").show();
                }
            }
        },
        register(){
            $("button[type=submit]").css('opacity', '.75').attr('disabled', true).html('<span class="loading-circle "></span> CREATING ACCOUNT...');
            axios.get('/sanctum/csrf-cookie').then(response => {
                axios.post('/api/register', this.form).then(()=>{
                    this.$router.push('shop');
                    this.$emit('authentication', true);
                    console.log('saved');
                }).catch((err)=>{
                    $("button[type=submit]").css('opacity', '1').attr('disabled', false).html('SIGNUP');
                    this.errors = err.response.data.errors;
                    this.$emit('alertNotification', this.errors);
                });
            });
        }
    },
}
</script>
