<template>
  <div v-if="display == 'checkbox'">
    <li v-for="item in playlist" :key="item.id" style="list-style-type: none">
      <input type="checkbox" class="playlist"
        :value="item.id" 
        :id="'playlist_'+item.id"
        :ref="'playlist_'+item.id"
        @change="updateSelectedPlaylist(item.id); 
        $emit('childClicked', item.id, $refs['playlist_'+item.id][0].checked )"
      />
      <label :for ="'playlist_'+item.id" :ref="'label_'+item.id">
        {{item.nama_playlist | capitalize}}
      </label>
      <ul>
        <playlist 
          :playlist="item.children" 
          :display="display" 
          v-on:childClicked="resonateParent(item.id, ...arguments)"
        ></playlist>
      </ul>
    </li>
  </div>
  <div v-else-if="display == 'browse'">
    <li v-for="item in playlist" :key="item.id" style="list-style-type: none">
      <label :for ="'playlist'+item.id">{{item.nama_playlist | capitalize}}</label>
      <ul>
        <playlist :playlist="item.children" :display="display"></playlist>
      </ul>
    </li>
  </div>
</template>

<script>
  import {mapMutations, mapGetters} from 'vuex'

  export default {
    name: 'playlist',
    props: {
      playlist: Array,
      display: String,
      selectedPlaylist: Array,
    },
    data(){
      return{
        playlist_checkbox: [],
      }
    },
    methods:{
      updateSelectedPlaylist(value){// console.log(value);
        this.$store.commit('SET_SELECTED_PLAYLIST', value);
      },
      resonateParent(parent_id, child_id, child_value){
        /*if(child_value === true){
          this.$refs['playlist_'+parent_id][0].click();
        }else{
          
        }*/       
      }
    },
    computed: {
      ...mapGetters([
        'getSelectedPlaylist'
      ]),
      ...mapMutations([
        'SET_SELECTED_PLAYLIST'
      ]),
    },
  }
</script>