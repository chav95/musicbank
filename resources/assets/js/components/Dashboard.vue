<template>
    <div class="container">
        <div class="justify-content-center mt-4 mb-4 h-100">
            <div class="col-12">
                <div class="card my-auto">
                    <h2>
                        Dashboard <br>
                        <small>App Usage Report</small>
                    </h2>
                    <div class="card-header">

                    </div>
                    
                    <div class="row mt-5 mb-4">
                        <div class="col-md-4">
                            <div class="box container-box">
                                <h4>Most Downloaded This Month</h4>
                                <apex-chart :chartType="uploaded.charType"
                                    :options="uploaded.options" :series="uploaded.series" :extra="uploaded.tooltip">
                                </apex-chart>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="box container-box">
                                <h4>Uploaded Music (Last 6 Months)</h4>
                                <apex-chart :chartType="uploadedBar.chartType" 
                                    :options="uploadedBar.options" :series="uploadedBar.series">
                                </apex-chart>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="box container-box">
                                <h4>Uploaded Music This Month (By Playlist)</h4>
                                <apex-chart :chartType="uploadedPie.chartType" 
                                    :options="uploadedPie.chartOptions" :series="uploadedPie.series">
                                </apex-chart>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import moment from 'moment';
    import ApexChart from './reusables/Apexchart.vue';

    export default {
        components:{
            ApexChart,
        },
        data(){
            return{
                uploaded: {
                    options: {
                        chart: {
                            id: 'most-downloaded',
                            type: 'bar',
                        },
                        xaxis: {
                            categories: ['Music 1', 'Music 2', 'Music 3', 'Music 4', 'Music 5']
                        },
                    },
                    series: [{
                        name: 'Download',
                        data: [91, 82, 70, 63, 41]
                    }]
                },
                uploadedBar: {
                    options: {
                        chart: {
                            id: 'uploaded-monthly',
                            type: 'bar',
                        },
                        xaxis: {
                            categories: ['Jan 2020', 'Dec 2019', 'Nov 2019', 'Oct 2019', 'Sep 2019', 'Aug 2019',]
                        },
                        plotOptions: {
                            bar: {
                                horizontal: true,
                            }
                        },
                    },
                    series: [{
                        name: 'Upload',
                        data: [50, 49, 60, 70, 91, 82]
                    }]
                },
                uploadedPie: {
                    chartType: 'pie',
                    chartOptions: {
                        labels: ['01_SOUND_EFFECTS', '02_MUSIC_LIBRARIAN', '03_VOICE_OVER'],
                        responsive: [{
                            breakpoint: 480,
                            options: {
                                chart: {
                                    width: 200
                                },
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }]
                    },
                    options: {
                        chart: {
                            id: 'uploaded-category'
                        },
                        labels: ['01_SOUND_EFFECTS', '02_MUSIC_LIBRARIAN', '03_VOICE_OVER']
                    },
                    series: [49, 60, 91]
                },
                
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
    .row{
        margin: 20px 0;
    }
    .justify-content-center{
        margin-right: -15px;
        margin-left: -15px;
    }
</style>