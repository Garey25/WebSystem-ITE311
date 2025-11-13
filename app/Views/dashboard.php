<?= $this->extend('template') ?>

<?= $this->section('content') ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Dashboard</h1>
        <a href="<?= base_url('logout') ?>" class="btn btn-outline-danger">Logout</a>
    </div>

    <div class="alert alert-success" role="alert">
        Welcome, <?= esc(session('userEmail')) ?>!
    </div>

    <!-- Alert container for messages -->
    <div id="alert-container" class="mt-3"></div>

    <?php if (session('role') === 'student'): ?>
        <!-- Enrolled Courses Section -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">My Enrolled Courses</h5>
            </div>
            <div class="card-body">
                <div id="enrolled-courses">
                    <?php if (isset($enrolledCourses) && !empty($enrolledCourses)): ?>
                        <div class="list-group">
                            <?php foreach ($enrolledCourses as $enrollment): ?>
                                <div class="list-group-item mb-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?= esc($enrollment['course_title']) ?></h6>
                                            <p class="mb-1 text-muted"><?= esc($enrollment['course_description']) ?></p>
                                            <small class="text-muted">Enrolled: <?= date('M d, Y', strtotime($enrollment['enrolled_at'])) ?></small>
                                        </div>
                                        <span class="badge bg-success">Enrolled</span>
                                    </div>
                                    
                                    <!-- Course Materials Section -->
                                    <?php if (!empty($enrollment['materials'])): ?>
                                        <div class="mt-3 pt-3 border-top">
                                            <h6 class="mb-2 text-primary">
                                                <i class="bi bi-file-earmark"></i> Course Materials (<?= count($enrollment['materials']) ?>)
                                            </h6>
                                            <div class="list-group list-group-flush">
                                                <?php foreach ($enrollment['materials'] as $material): ?>
                                                    <div class="list-group-item px-0 py-2 bg-transparent">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <i class="bi bi-file-earmark-text me-2"></i>
                                                                <span><?= esc($material['file_name']) ?></span>
                                                                <small class="text-muted ms-2">
                                                                    (<?= date('M j, Y', strtotime($material['created_at'])) ?>)
                                                                </small>
                                                            </div>
                                                            <a href="<?= site_url('materials/download/' . $material['id']) ?>" 
                                                               class="btn btn-sm btn-outline-primary">
                                                                <i class="bi bi-download"></i> Download
                                                            </a>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="mt-2 pt-2 border-top">
                                            <small class="text-muted">
                                                <i class="bi bi-info-circle"></i> No materials available for this course yet.
                                            </small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">You are not enrolled in any courses yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Available Courses Section -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Available Courses</h5>
            </div>
            <div class="card-body">
                <div id="available-courses">
                    <?php if (isset($availableCourses) && !empty($availableCourses)): ?>
                        <div class="list-group">
                            <?php foreach ($availableCourses as $course): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1"><?= esc($course['title']) ?></h6>
                                        <p class="mb-1 text-muted"><?= esc($course['description']) ?></p>
                                    </div>
                                    <button class="btn btn-primary btn-sm enroll-btn" 
                                            data-course-id="<?= $course['id'] ?>"
                                            data-course-title="<?= esc($course['title']) ?>">
                                        Enroll
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">No courses available at the moment.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php elseif (session('role') === 'admin' || session('role') === 'teacher'): ?>
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Manage Course Materials</h5>
            </div>
            <div class="card-body">
                <?php if (isset($allCourses) && !empty($allCourses)): ?>
                    <div class="list-group">
                        <?php foreach ($allCourses as $course): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1"><?= esc($course['title']) ?></h6>
                                    <p class="mb-1 text-muted"><?= esc($course['description']) ?></p>
                                </div>
                                <a class="btn btn-primary btn-sm" href="<?= site_url('admin/course/' . $course['id'] . '/upload') ?>">Upload Material</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">No courses available.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- jQuery and AJAX Script -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Handle enroll button clicks
            $('.enroll-btn').on('click', function(e) {
                e.preventDefault();
                
                const button = $(this);
                const courseId = button.data('course-id');
                const courseTitle = button.data('course-title');
                
                // Disable button and show loading state
                button.prop('disabled', true).text('Enrolling...');
                
                // Send AJAX request
                $.ajax({
                    url: '<?= base_url('course/enroll') ?>',
                    type: 'POST',
                    data: {
                        course_id: courseId,
                        <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log('Response:', response); // Debug log
                        if (response.success) {
                            // Show success message
                            showAlert('success', response.message);
                            
                            // Hide the button and show enrolled status
                            button.replaceWith('<span class="badge bg-success">Enrolled</span>');
                            
                            // Add course to enrolled courses list
                            addToEnrolledCourses(courseId, courseTitle);
                        } else {
                            // Show error message
                            showAlert('danger', response.message);
                            
                            // Re-enable button
                            button.prop('disabled', false).text('Enroll');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('Error:', xhr.responseText); // Debug log
                        // Show error message
                        showAlert('danger', 'An error occurred. Please try again.');
                        
                        // Re-enable button
                        button.prop('disabled', false).text('Enroll');
                    }
                });
            });
            
            // Function to show alert messages
            function showAlert(type, message) {
                const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                $('#alert-container').html(alertHtml);
                
                // Auto-hide after 5 seconds
                setTimeout(function() {
                    $('.alert').fadeOut();
                }, 5000);
            }
            
            // Function to add course to enrolled courses list
            function addToEnrolledCourses(courseId, courseTitle) {
                const enrolledCoursesDiv = $('#enrolled-courses');
                const currentDate = new Date().toLocaleDateString('en-US', { 
                    year: 'numeric', 
                    month: 'short', 
                    day: 'numeric' 
                });
                
                // Check if there are existing enrolled courses
                if (enrolledCoursesDiv.find('.list-group').length === 0) {
                    enrolledCoursesDiv.html('<div class="list-group"></div>');
                }
                
                // Add new enrolled course
                const newCourseHtml = `
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">${courseTitle}</h6>
                            <p class="mb-1 text-muted">Course description</p>
                            <small class="text-muted">Enrolled: ${currentDate}</small>
                        </div>
                        <span class="badge bg-success">Enrolled</span>
                    </div>
                `;
                
                enrolledCoursesDiv.find('.list-group').append(newCourseHtml);
            }
        });
    </script>

<style>
body {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    position: relative;
    overflow: auto;
}

body::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    animation: rotate 20s linear infinite;
    z-index: -1;
}

@keyframes rotate {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.container {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 24px;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.2);
    position: relative;
    z-index: 1;
}

h1, h2, h3, h4, h5, h6, p, a, span, div {
    color: #000000 !important;
}
</style>
<?= $this->endSection() ?>