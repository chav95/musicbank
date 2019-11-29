<template>
    <div :class="'progress '+showhide">
        <div :class="'progress-bar '+progressClass+' progress-bar-striped active'"
            role="progressbar" 
            :aria-valuenow="uploadProgress" 
            aria-valuemin="0" 
            aria-valuemax="100" :style="'width:'+uploadProgress+'%'"
        >{{progressLabel}}</div>
    </div>
</template>

<script>
    export default {
        props: {
            uploadProgress: Number,
            uploadResult: String,
            isUploading: Boolean,
        },
        data(){
            return{
                
            }
        },
        computed: {
            progressLabel(){
                if(this.uploadProgress < 100){
                    return '('+this.uploadProgress+'%) Uploading...';
                }else{
                    if(this.uploadResult == 'error'){
                        return 'Upload Failed';
                    }else if(this.uploadResult == 'success'){
                        return 'Upload Complete';
                    }
                }
            },
            progressClass(){
                if(this.uploadProgress < 100){
                    return '';
                }else{
                    if(this.uploadResult == 'error'){
                        return 'progress-bar-danger';
                    }else if(this.uploadResult == 'success'){
                        return 'progress-bar-success';
                    }
                }
            },
            showhide(){
                if(this.isUploading === false){
                    return 'd-none';
                }else{
                    return '';
                }
            }
        },
    }
</script>

<style scoped>

</style>