<?= $this->extend('template') ?>

<?= $this->section('content') ?>

<div class="row mb-4">
    <div class="col-md-6">
        <form id="searchForm" class="d-flex">
            <div class="input-group">
                <input type="text" id="searchInput" class="form-control"
                    placeholder="Search courses..." name="search_term">
                <button class="btn btn-outline-primary" type="submit">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
        </form>
    </div>
</div>

<div id="coursesContainer" class="row">
    <?php foreach ($courses as $course): ?>
        <div class="col-md-4 mb-4 course-item">
            <div class="card course-card">
                <div class="card-body">
                    <h5 class="card-title"><?= esc($course['title']) ?></h5>
                    <p class="card-text"><?= esc($course['description'] ?? 'No description available') ?></p>
                    <a href="<?= site_url('courses/view/' . $course['id']) ?>" class="btn btn-primary">View Course</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    var baseUrl = "<?= site_url('courses/view/') ?>";
    
    // Client side filtering
    $("#searchInput").on("keyup", function() {
        var searchTerm = $(this).val().toLowerCase();
        $(".course-item").each(function() {
            var courseTitle = $(this).text().toLowerCase();
            $(this).toggle(courseTitle.indexOf(searchTerm) > -1);
        });
    });

    // Server side search with AJAX
    $("#searchForm").on("submit", function(e) {
        e.preventDefault();
        var searchTerm = $("#searchInput").val();
        
        $.ajax({
            url: "<?= site_url('courses/search') ?>",
            type: "GET",
            data: { search: searchTerm },
            success: function(response) {
                var coursesContainer = $("#coursesContainer");
                coursesContainer.empty();
                
                if(response.length > 0) {
                    $.each(response, function(index, course) {
                        var courseItem = 
                        `<div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">` + course.name + `</h5>
                                    <p class="card-text">` + course.description + `</p>
                                    <a href="` + baseUrl + course.id + `" class="btn btn-primary">View Course</a>
                                </div>
                            </div>
                        </div>`;
                        coursesContainer.append(courseItem);
                    });
                } else {
                    coursesContainer.html(
                        '<div class="col-12"><div class="alert alert-info">No courses found matching your search.</div></div>'
                    );
                }
            },
            error: function() {
                var coursesContainer = $("#coursesContainer");
                coursesContainer.html(
                    '<div class="col-12"><div class="alert alert-danger">An error occurred while searching. Please try again.</div></div>'
                );
            }
        });
    });
});
</script>

<style>
.course-card {
    background-color: rgba(0, 48, 73, 0.6);
    border: 1px solid rgba(247, 127, 0, 0.3);
    border-radius: 1rem;
    color: #EAE2B7;
    height: 100%;
}

.course-card .card-body {
    padding: 1.5rem;
}

.course-card .card-title {
    color: #FCBF49;
    font-weight: 600;
    margin-bottom: 1rem;
}

.course-card .card-text {
    color: #EAE2B7;
    margin-bottom: 1.5rem;
}

.course-card .btn-primary {
    background-color: #F77F00;
    border-color: #F77F00;
    color: #003049;
}

.course-card .btn-primary:hover {
    background-color: #FCBF49;
    border-color: #FCBF49;
    color: #003049;
}
</style>

<?= $this->endSection() ?>

