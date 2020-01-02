<template>
    <apexchart :width="width" :type="chartType" :options="options" :series="series" ref="chart"></apexchart>
</template>

<script>
    import moment from 'moment';

    export default {
        //type: bar, line, donut, pie
        props:{
            chartType: {
                type: String,
                default: '',
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
            updateChart(){
                this.$refs.chart.updateSeries([{
                    data: makeData()
                }])
            }
        },
        watch:{
            chartType: function(){ console.log('changed');
                var chart = new ApexCharts(document.querySelector('#'+this.options.chart.id), this.options);
                chart.render();
            }
        },
        mounted(){

        }
    }
</script>

<style scoped>

</style>