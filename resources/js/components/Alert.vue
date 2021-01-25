<template>
    <div class="alert-message">
        <div v-for="alert in alerts" v-bind:key="alert.id" v-bind:class="getClass(alert[0])">
            {{alert[0]}}<span @click="closeAlert($event,alert.id)">&times;</span>
        </div>
        <!-- <div v-bind:class="getClass(alerts.message)" v-if="alerts.message">
            {{alerts.errors.email[0]}} <span @click="closeAlert($event)">&times;</span>
        </div> -->
    </div>
</template>

<script>
import {v4 as uuidv4} from 'uuid';
export default {
    name: "Alert",
    props:["alerts"],
    data() {
        return {
            notification: ""
        }
    },
    mounted() {
    }, 
    methods: {
        getClass(data){
            return{
                'alert-msg alert-error' : data,
                'alert-msg alert-success': !data
            }
        },
        closeAlert(event){
            // this.notification = uuidv4();
            $(event.target).closest('.alert-msg').hide();    
        }
    },
}
</script>