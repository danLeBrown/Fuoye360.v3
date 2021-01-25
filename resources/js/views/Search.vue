<template>
    <div>
        <!-- <div class="form-container wrapper" style="padding:0;">
            <form>
                <div class="input-div search-div">
                    <input type="text">
                    <button><i class="fas fa-search"></i> SEARCH</button>
                </div>
                
            </form>
        </div> -->
        <div class="wrapper search-wrapper">
        </div>
    </div>
</template>
<style scoped>
    form{
        padding: 0;
        /* margin: 0 1rem; */
    }
    .search-div{
        background: var(--white-color);
    }
    .search-div input{
        flex: 9;
        padding: .5rem !important;
    }
    .search-div button{
        flex: 3;
    }
    .search-wrapper{
        min-height: calc(100vh - 59px);
    }
</style>