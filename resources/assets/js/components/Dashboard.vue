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
                                <h4>Most Downloaded This Month (Hardcode Data)</h4>
                                <apex-chart :chartType="uploadedTest.charType"
                                    :options="uploadedTest.options" :series="uploadedTest.series" :extra="uploadedTest.tooltip">
                                </apex-chart>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="box container-box">
                                <h4>Uploaded Music (Hardcode Data)</h4>
                                <apex-chart :chartType="uploadedBarTest.chartType" 
                                    :options="uploadedBarTest.options" :series="uploadedBarTest.series">
                                </apex-chart>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="box container-box">
                                <h4>Uploaded Music This Month (Hardcode Data)</h4>
                                <apex-chart :chartType="uploadedPieTest.chartType" 
                                    :options="uploadedPieTest.chartOptions" :series="uploadedPieTest.series">
                                </apex-chart>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="col-md-6">
                            <div class="box container-box">
                                <h4>Most Downloaded This Month</h4>
                                <apex-chart :chartType="mostDownload.charType"
                                    :options="mostDownload.options" :series="mostDownload.series">
                                </apex-chart>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="box container-box">
                                <h4>Uploaded Music (Last 6 Months)</h4>
                                <apex-chart :chartType="uploadedBar.chartType" 
                                    :options="uploadedBar.options" :series="uploadedBar.series">
                                </apex-chart>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="col-md-6">
                            <div class="box container-box">
                                <h4>Total Music Composition (By Playlist)</h4>
                                <apex-chart :chartType="musicCompositionPie.chartType" 
                                    :options="musicCompositionPie.chartOptions" :series="musicCompositionPie.series">
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

                uploadedTest: {
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
                uploadedBarTest: {
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
                uploadedPieTest: {
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

                uploadedBar: {
                    options: {
                        chart: {
                            id: 'uploaded-monthly',
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
                    },
                    series: [{
                        name: 'Download',
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
            }
        },
        methods: {
            loadData(){
                axios.get(window.location.origin+'/api/music/getUploadedMusicPerMonth').then(({data}) => (this.music_data = data));
                axios.get(window.location.origin+'/api/log').then(({data}) => (this.log_data = data));
                axios.get(window.location.origin+'/api/playlist').then(({data}) => (this.playlist_data = data));
            }
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
                    },
                    series: [{
                        name: 'Download',
                        data: seriesData
                    }]
                }};
            });
            
            axios.get(window.location.origin+'/api/music/getUploadedMusicPerMonth').then(({data}) => {
                this.music_data = data
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
                        name: 'Uploads',
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