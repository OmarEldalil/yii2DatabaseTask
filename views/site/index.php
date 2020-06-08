<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<div class="site-index" id="app" v-cloak>
    <div>
        <div v-if="!categories.length">
            <h2 class="text-censter">It seems like you don't have categories in your database</h2>
            <div class="text-center">
                <a href="http://localhost:8080/index.php?r=site/seed" class="btn btn-primary">Seed the database</a>
            </div>
        </div>
        <div class="form-group" v-for="(category,index) in categories">
            <label :for="'category' + index">{{`Category ${index+1}`}}</label>
            <div>
                <select name="category" class="customSelect" @change="requestSubCategories(index,$event)">
                    <option value="">Please Select Category</option>
                    <option v-for="subCategory in category" :value="subCategory.id">{{subCategory.name}}</option>
                </select>
            </div>
        </div>
        <div v-if="endReached && categories.length">
            <h1 class="text-center">No More Categories</h1>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.2/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>

<script>
    let app = new Vue({
        el: '#app',
        data: {
            categories: [],
            endReached: false
        },
        methods: {
            requestSubCategories(depth, e) {
                this.categories = this.categories.slice(0, depth + 1);
                this.getCategory(e.target.value);
            },
            getCategory(id = null) {
                axios
                    .get(`http://localhost:8080/index.php?r=site/getcategory&id=${id}`)
                    .then(res => {
                        this.endReached = false;
                        let subCategories = res.data;
                        if (res.data.length) {
                            return this.categories.push(res.data);
                        }
                        this.endReached = true

                    })
                    .catch(e => console.log(e))
            }
        },
        mounted() {
            this.getCategory();
        }
    })
</script>
