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
    <?php foreach ($courses as $course): 
        $isEnrolled = isset($enrollments[$course['id']]) && $enrollments[$course['id']]['status'] === 'approved';
        $isPending = isset($enrollments[$course['id']]) && $enrollments[$course['id']]['status'] === 'pending';
    ?>
        <div class="col-md-4 mb-4 course-item" data-course-id="<?= $course['id'] ?>">
            <div class="card course-card">
                <div class="card-body">
                    <h5 class="card-title"><?= esc($course['title']) ?></h5>
                    <p class="card-text"><?= esc($course['description'] ?? 'No description available') ?></p>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="<?= site_url('courses/view/' . $course['id']) ?>" class="btn btn-outline-primary btn-sm">View Details</a>
                        
                        <div class="enrollment-status" id="enroll-status-<?= $course['id'] ?>">
                            <?php if ($isEnrolled): ?>
                                <span class="badge bg-success">Enrolled</span>
                            <?php elseif ($isPending): ?>
                                <span class="badge bg-warning">Pending Approval</span>
                            <?php else: ?>
                                <button class="btn btn-primary btn-sm enroll-btn" 
                                        data-course-id="<?= $course['id'] ?>"
                                        data-course-title="<?= esc($course['title']) ?>">
                                    Enroll Now
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Toast Notifications -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="enrollmentToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto" id="toast-title">Notification</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toast-message">
            <!-- Message will be inserted here -->
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize toasts
    const toastEl = document.getElementById('enrollmentToast');
    const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
    
    // Show toast notification
    function showToast(title, message, type = 'info') {
        const toastTitle = $('#toast-title');
        const toastMessage = $('#toast-message');
        
        // Update toast content
        toastTitle.text(title);
        toastMessage.html(message);
        
        // Update toast style based on type
        const toast = $('#enrollmentToast');
        toast.removeClass('bg-success bg-warning bg-danger bg-info');
        
        switch(type) {
            case 'success':
                toast.addClass('bg-success text-white');
                break;
            case 'warning':
                toast.addClass('bg-warning text-dark');
                break;
            case 'error':
                toast.addClass('bg-danger text-white');
                break;
            default:
                toast.addClass('bg-info text-white');
        }
        
        // Show the toast
        toast.toast('show');
    }

    // Handle enroll button click
    $(document).on('click', '.enroll-btn', function() {
        const button = $(this);
        const courseId = button.data('course-id');
        const courseTitle = button.data('course-title');
        
        // Disable button and show loading state
        button.prop('disabled', true).html(`
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Processing...
        `);
        
        // Send enrollment request
        $.ajax({
            url: '<?= site_url('courses/enroll') ?>',
            method: 'POST',
            data: {
                course_id: courseId,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Update the UI to show pending status
                    $(`#enroll-status-${courseId}`).html(`
                        <span class="badge bg-warning">Pending Approval</span>
                    `);
                    
                    showToast('Success', response.message, 'success');
                } else {
                    // Re-enable button if there was an error
                    button.prop('disabled', false).text('Enroll Now');
                    showToast('Error', response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Enrollment error:', error);
                button.prop('disabled', false).text('Enroll Now');
                showToast('Error', 'An error occurred. Please try again.', 'error');
            }
        });
    });

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
                        // You might want to update this to include the enrollment status
                        var courseItem = `
                        <div class="col-md-4 mb-4 course-item" data-course-id="${course.id}">
                            <div class="card course-card">
                                <div class="card-body">
                                    <h5 class="card-title">${course.name}</h5>
                                    <p class="card-text">${course.description || 'No description available'}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="<?= site_url('courses/view/') ?>${course.id}" class="btn btn-outline-primary btn-sm">View Details</a>
                                        <div class="enrollment-status" id="enroll-status-${course.id}">
                                            <button class="btn btn-primary btn-sm enroll-btn" 
                                                    data-course-id="${course.id}"
                                                    data-course-title="${course.name}">
                                                Enroll Now
                                            </button>
                                        </div>
                                    </div>
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

