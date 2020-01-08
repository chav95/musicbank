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
                    <div>
                        <div class="col-md-4">
                            <div class="box container-box">
                                <h4>Most Downloaded This Month</h4>
                                <apex-chart :chartType="mostDownload.charType"
                                    :options="mostDownload.options" :series="mostDownload.series">
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
                                <h4>Storage Usage (Last 6 Months)</h4>
                                <apex-chart :chartType="monthlyStorage.charType"
                                    :options="monthlyStorage.options" :series="monthlyStorage.series">
                                </apex-chart>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="col-md-6">
                            <div class="box container-box">
                                <h4>Total Music Composition</h4>
                                <apex-chart :chartType="musicCompositionPie.chartType" 
                                    :options="musicCompositionPie.chartOptions" :series="musicCompositionPie.series">
                                </apex-chart>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="box container-box">
                                <h4>Total Music Storage Usage (In Giga Bytes)</h4>
                                <apex-chart :chartType="totalStorageUsage.chartType" 
                                    :options="totalStorageUsage.chartOptions" :series="totalStorageUsage.series">
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
                music_data: null,
                log_data: null,
                playlist_data: null,
                storage_month_data: null,
                storage_total_data: null,

                uploadedBar: {
                    options: {
                        chart: {
                            id: 'uploaded-monthly',
                            type: 'line',
                        },
                        xaxis: {
                            categories: []
                        },
                    },
                    series: [{
                        name: 'Uploads',
                        data: []
                    }]
                },
                mostDownload: {
                    options: {
                        chart: {
                            id: 'most-downloaded',
                            type: 'bar',
                        },
                        xaxis: {
                            categories: []
                        },
                        plotOptions: {
                            bar: {
                                horizontal: true,
                            }
                        },
                    },
                    series: [{
                        name: 'Download',
                        data: []
                    }]
                },
                monthlyStorage: {
                    options: {
                        chart: {
                            id: 'storage-monthly',
                            type: 'line',
                        },
                        xaxis: {
                            categories: []
                        },
                        yaxis: {
                            title: {
                                text: '(in Giga Bytes)'
                            }
                        }
                    },
                    series: [{
                        name: 'Storage Usage (In GB)',
                        data: []
                    }]
                },
                musicCompositionPie: {
                    chartType: 'pie',
                    chartOptions: {
                        labels: [],
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
                        labels: []
                    },
                    series: []
                },
                totalStorageUsage: {
                    chartType: 'pie',
                    chartOptions: {
                        labels: [],
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
                        labels: []
                    },
                    series: []
                },
            }
        },
        methods: {
            
        },
        mounted(){
            axios.get(window.location.origin+'/api/log/getMostDownloadThisMonth').then(({data}) => {
                this.log_data = data;
                let categoryLabel = [];
                let seriesData = [];

                for(let item of data){ console.log(item.music.judul);
                    categoryLabel.push(item.music.judul);
                    seriesData.push(item.download);
                }

                this.mostDownload = {...this.mostDownload, ...{
                    options: {
                        chart: {
                            id: 'most-downloaded',
                            type: 'bar',
                        },
                        xaxis: {
                            categories: categoryLabel
                        },
                        plotOptions: {
                            bar: {
                                horizontal: true,
                            }
                        },
                    },
                    series: [{
                        name: 'Download',
                        data: seriesData
                    }]
                }};
            });
            
            axios.get(window.location.origin+'/api/music/getUploadedMusicPerMonth').then(({data}) => {
                this.music_data = data;
                let categoryLabel = [];
                let seriesData = [];
                for(let item of data){
                    categoryLabel.push(item.label);
                    seriesData.push(item.data);
                }

                this.uploadedBar = {...this.uploadedBar, ...{
                    options: {
                        chart: {
                            id: 'uploaded-monthly',
                            type: 'line',
                        },
                        xaxis: {
                            categories: categoryLabel
                        },
                    },
                    series: [{
                        name: 'Uploads',
                        data: seriesData
                    }]
                }};
            });

            axios.get(window.location.origin+'/api/music/getStorageUsagePerMonth').then(({data}) => {
                this.storage_month_data = data;
                let categoryLabel = [];
                let seriesData = [];
                for(let item of data){
                    categoryLabel.push(item.label);
                    seriesData.push(item.data);
                }

                this.monthlyStorage = {...this.monthlyStorage, ...{
                    options: {
                        chart: {
                            id: 'storage-monthly',
                            type: 'line',
                        },
                        xaxis: {
                            categories: categoryLabel
                        },
                        yaxis: {
                            title: {
                                text: '(in Giga Bytes)'
                            }
                        }
                    },
                    series: [{
                        name: 'Storage Usage (In GB)',
                        data: seriesData
                    }]
                }};
            });

            axios.get(window.location.origin+'/api/playlist/getOuterPlaylistComposition').then(({data}) => {
                this.playlist_data = data;
                let categoryLabel = [];
                let seriesData = [];
                for(let item of data){
                    categoryLabel.push(item.label);
                    seriesData.push(item.number);
                }

                this.musicCompositionPie = {...this.musicCompositionPie,... {
                    chartType: 'pie',
                    chartOptions: {
                        labels: categoryLabel,
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
                        labels: categoryLabel
                    },
                    series: seriesData
                }};
            });

            axios.get(window.location.origin+'/api/playlist/getOuterPlaylistFilesize').then(({data}) => {
                this.storage_total_data = data;
                let categoryLabel = [];
                let seriesData = [];
                for(let item of data){
                    categoryLabel.push(item.label);
                    seriesData.push(item.number);
                }

                this.totalStorageUsage = {...this.totalStorageUsage,... {
                    chartType: 'pie',
                    chartOptions: {
                        labels: categoryLabel,
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
                        labels: categoryLabel
                    },
                    series: seriesData
                }};
            });
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