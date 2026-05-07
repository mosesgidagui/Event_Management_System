<?php 
include 'admin/db_connect.php'; 
?>
<style>
#portfolio .img-fluid{
    width: calc(100%);
    height: 30vh;
    z-index: -1;
    position: relative;
    padding: 1em;
}
.event-list{
cursor: pointer;
}
span.hightlight{
    background: yellow;
    
}
.banner{
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 26vh;
        width: calc(30%);
    }
    .banner img{
        width: calc(100%);
        height: calc(100%);
        cursor :pointer;
    }
.event-list{
cursor: pointer;
border: unset;
flex-direction: inherit;
}

.event-list .banner {
    width: calc(40%)
}
.event-list .card-body {
    width: calc(60%)
}
.event-list .banner img {
    border-top-left-radius: 5px;
    border-bottom-left-radius: 5px;
    min-height: 50vh;
}
span.hightlight{
    background: yellow;
}
.banner{
   min-height: calc(100%)
}
</style>
        <header class="masthead">
            <div class="container-fluid h-100">
                <div class="row h-100 align-items-center justify-content-center text-center">
                    <div class="col-lg-8 align-self-end mb-4 page-title">
                    	<h3 class="text-white">Welcome to <?php echo $_SESSION['system']['name']; ?></h3>
                        <hr class="divider my-4" />

                    <div class="col-md-12 mb-2 justify-content-center">
                    </div>                        
                    </div>
                    
                </div>
            </div>
        </header>
            <div class="container mt-3 pt-2">
                <h4 class="text-center text-white">Upcoming Events</h4>
                <hr class="divider">
                <div class="card mb-4">
                    <div class="card-body">
                        <form id="filter-form" method="get" action="index.php" class="row align-items-end">
                            <input type="hidden" name="page" value="home">
                            <div class="form-group col-md-5">
                                <label class="control-label">Search</label>
                                <input type="text" class="form-control" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES) ?>" placeholder="Event name, venue, or description">
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label">Date</label>
                                <input type="date" class="form-control" name="date" value="<?php echo htmlspecialchars($_GET['date'] ?? '', ENT_QUOTES) ?>">
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label">Category</label>
                                <select class="custom-select" name="category">
                                    <?php
                                    $selectedCategory = $_GET['category'] ?? 'all';
                                    $categories = $conn->query("SELECT DISTINCT category FROM events ORDER BY category ASC");
                                    ?>
                                    <option value="all" <?php echo $selectedCategory === 'all' ? 'selected' : '' ?>>All</option>
                                    <?php while($cat = $categories->fetch_assoc()): ?>
                                    <option value="<?php echo htmlspecialchars($cat['category'], ENT_QUOTES) ?>" <?php echo $selectedCategory === $cat['category'] ? 'selected' : '' ?>><?php echo ucwords($cat['category']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group col-md-1">
                                <button class="btn btn-primary btn-block" id="filter-go">Go</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div id="events-container">
                    <?php include 'events_list.php'; ?>
                </div>
                
            </div>


<script>
    // handle read more and image viewer via event delegation
    document.getElementById('events-container').addEventListener('click', function(e){
        var t = e.target;
        if(t.classList.contains('read_more')){
            var id = t.getAttribute('data-id');
            location.href = 'index.php?page=view_event&id='+id;
        }
        if(t.classList.contains('rsvp_btn')){
            var id = t.getAttribute('data-id');
            // POST to RSVP endpoint
            fetch('admin/ajax.php?action=save_register', {
                method: 'POST',
                headers: {'Content-Type':'application/x-www-form-urlencoded'},
                body: 'event_id='+encodeURIComponent(id)
            }).then(function(r){ return r.text(); })
            .then(function(resp){
                resp = resp.trim();
                if(resp == '5'){
                    // not logged in as student
                    alert('Please log in with a student account to RSVP.');
                    window.location = 'index.php?page=login';
                    return;
                }
                if(resp == '2'){
                    alert('You have already RSVPed for this event.');
                    return;
                }
                if(resp == '1'){
                    alert('RSVP successful — check your RSVP history.');
                    // optionally refresh RSVP history or update UI
                    return;
                }
                alert('Could not complete RSVP. Response: '+resp);
            }).catch(function(err){ console.error(err); alert('Network error'); });
        }
    });

    // AJAX filter submission
    document.getElementById('filter-form').addEventListener('submit', function(e){
        e.preventDefault();
        var form = this;
        var params = new URLSearchParams(new FormData(form));
        // fetch the events list
        fetch('events_list.php?'+params.toString())
        .then(function(r){ return r.text(); })
        .then(function(html){
            document.getElementById('events-container').innerHTML = html;
        })
        .catch(function(err){ console.error(err); });
    });

    // live search (debounced)
    (function(){
        var timer = null;
        var input = document.querySelector('input[name="search"]');
        if(!input) return;
        input.addEventListener('input', function(){
            clearTimeout(timer);
            timer = setTimeout(function(){
                document.getElementById('filter-form').dispatchEvent(new Event('submit'));
            }, 350);
        });
    })();
</script>