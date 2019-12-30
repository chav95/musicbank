<template>
    <apexchart :width="width" :type="chartType" :options="options" :series="series"></apexchart>
</template>

<script>
    import moment from 'moment';

    export default {
        //type: bar, line, donut, pie
        props:{
            chartType: {
                type: String,
                default: 'bar',
            },
            width: Number,
            options: Object,
            series: Array,
        },
        data(){
            return{
                
            }
        },
        methods: {
            loadData(){
                axios.get(window.location.origin+'/api/');
            }
        },
        mounted(){

        }
    }
</script>

<style scoped>

</style>