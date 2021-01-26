<template>
    <div>
        <div class="wrapper">
            <div></div>
            <div id="notification-wrapper" v-if="notifications.length > 0">
                <div v-for="notification in notifications" v-bind:key="notification.id" class="notification-col">
                    <span :class="'notification-id_'+notification.id" style="display:none">{{notification.id}}</span>
                    <button :class="'delete-notification btnid_'+notification.sender.id" @click="deleteNotification(notification.id)">&times;</button>
                    
                    <div v-if="notification.type == 'follow'"><router-link :to="{name: 'visit', params:{username : notification.sender.name}, query:{v: 'shop'}}"><img loading="lazy" :src="asset('storage/profile_images/'+notification.sender.image)" :alt="notification.sender.name"> <span class="t_success notifier">{{notification.sender.name}}</span></router-link> followed you <i class='fas fa-user' style='color:limegreen;'></i><br><span class="time"> {{notification.relative_at}}</span></div>
                    
                    <div v-else-if="notification.type == 'new-product'"><router-link :to="{name: 'visit', params:{username: notification.sender.name}, query:{v: 'shop'}}"><img loading="lazy" :src="asset('storage/profile_images/'+notification.sender.image)" :alt="notification.sender.name"> <span class="t_success notifier">{{notification.sender.name}}</span></router-link> added a new product, <span style="font-weight:bold;"></span> <i class='fas fa-shopping-bag' style='color:limegreen;'></i><br><span class="time"> {{notification.relative_at}}</span></div>

                    <div v-else-if="notification.type == 'sales'"><router-link :to="{name: 'visit', params: {username: notification.sender.name}}"><img loading="lazy" :src="asset('storage/profile_images/'+notification.sender.image)" :alt="notification.sender.name"> <span class="t_success notifier">{{notification.sender.name}}</span></router-link> wants to buy <i class='fas fa-cart-plus' style='color:limegreen;'></i><span class="n-product">
                        <ul style="padding: 1rem 2rem 0;">
                            <li v-for="(product, index) in notification.data" v-bind:key="index"> {{product.qty + ' '+ product.name}}{{product.qty > 1? '(s)': ''}}</li>
                        </ul>
                    </span> <br><span class="time"> {{notification.relative_at}}</span></div>

                    <div v-else-if="notification.type == 'new-broadcast'"><router-link :to="{name: 'visit' ,params: {username: notification.sender.name}, query:{v: 'broadcast'}}"><img loading="lazy" :src="asset('storage/profile_images/'+notification.sender.image)" :alt="notification.sender.name"> <span class="t_success notifier">{{notification.sender.name}}</span></router-link> sent a new broadcast <i class='fas fa-bullhorn' style='color:limegreen;'></i><br><span class="time"> {{notification.relative_at}}</span></div>

                    <div v-else-if="notification.type == 'rebroadcast'"><router-link :to="{name:'visit', params: {username: notification.sender.name}, query:{v: 'broadcast'}}"><img loading="lazy" :src="asset('storage/profile_images/'+notification.sender.image)" :alt="notification.sender.name"> <span class="t_success notifier">{{notification.sender.name}}</span></router-link> rebroadcasted your broadcast <i class='fas fa-bullhorn' style='color:limegreen;'></i><br><span class="time"> {{notification.relative_at}}</span></div>

                    <div v-else-if="notification.type == 'like-broadcast'"><router-link :to="{name: 'visit', params: {username: notification.sender.name}, query: {v: 'broadcast'}}"><img loading="lazy" :src="asset('storage/profile_images/'+notification.sender.image)" :alt="notification.sender.name"> <span class="t_success notifier">{{notification.sender.name}}</span></router-link> liked your broadcast <i class='fas fa-heart' style='color:limegreen;'></i><br><span class="time"> {{notification.relative_at}}</span></div>
                
                    <div v-else-if="notification.type == 'comment-broadcast'"><router-link :to="{name: 'visit', params: {username: notification.sender.name}, query: {v: 'broadcast'}}"><img loading="lazy" :src="asset('storage/profile_images/'+notification.sender.image)" :alt="notification.sender.name"> <span class="t_success notifier">{{notification.sender.name}}</span></router-link> replied to your broadcast <i class='fas fa-comment' style='color:limegreen;'></i><br><span class="time"> {{notification.relative_at}}</span></div>
                    <div v-if="notification.new == true" class="new-notification"><i class="fas fa-bell"></i> NEW</div>
                </div>
                <infinite-loading @infinite="infiniteHandler"></infinite-loading>
            </div>
            <div v-else>
                <div class="shop-empty-col">
                    <img loading="lazy" :src="asset('assets/illustrations/new-message-animate.svg')" alt="">
                    <div>
                        <p>No new Notifications</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import InfiniteLoading from 'vue-infinite-loading';
export default {
    data() {
        return {
            notifications: [],
            api: '',
            page: 1,
        }
    },
    props: ["push_notifications_id"],
    components:{
        InfiniteLoading
    },
    created() {
        this.api = '/api/notifications';
        this.infiniteHandler();
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
                    res.data.data.forEach(element => {
                        if(this.push_notifications_id.includes(element.id)){
                            element.new = true;
                        }
                        element.data = element.data != null ? JSON.parse(element.data): null;
                    });
                    this.notifications.push(...res.data.data);
                    $state.loaded();
                } else {
                    $state.complete();
                }
            })
            .catch(err=>console.log(err));
        },
        deleteNotification(id){
            axios.post('/api/'+id+'/delete-notification')
            .then(res=>{
                this.notifications = this.notifications.filter(notification => notification.id != id);
            })
            .catch(err=>console.log(err));
        }
    },
}
</script>

<style scoped>
    #notification-wrapper {
        display: grid;
        border-radius: 5px;
        margin-top: .5rem;
    }
    #notification-wrapper img{
        width: 35px;
        height: 35px;
        border-radius: 50%;
        object-fit: cover;
    }

    .notification-col {
        padding: .4em;
        position: relative;
        border-radius: 1rem;
        -webkit-border-radius: 1rem;
        -moz-border-radius: 1rem;
        -ms-border-radius: 1rem;
        -o-border-radius: 1rem;
    }

    .notification-col:nth-child(odd) {
        background: var(--white-color);
    }

    .notification-col:nth-child(even) {
        background: var(--bg-color);
    }

    .n-product {
        font-weight: bold;
    }

    .delete-notification {
        position: absolute;
        right: 5px;
        font-size: 1.2rem;
        color: var(--red-color);
        cursor: pointer;
        background: none;
        border-radius: 50%;
        border: 1px solid var(--red-color);
        width: 20px;
        height: 20px;
    }

    .time {
        display: block;
        float: right;
        font-size: .7rem;
        color: var(--input-border-color);
    }

    .notification-col:nth-child(even) .time {
        color: var(--dark-color);
    }

    .notifier {
        font-weight: bold;
        color: var(--dark-color);
    }
    a{
        text-decoration: none;
    }
    .new-notification{
        display: block;
        float: right;
        background: var(--brand-color);
        color: #fff;
        padding: 0 .5rem;
        border-radius: 1rem;
        font-size: .55rem;
        margin-right: 1rem;
    }
</style>