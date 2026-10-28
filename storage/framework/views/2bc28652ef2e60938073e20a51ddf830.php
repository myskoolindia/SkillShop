<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop='static'>
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel"><?php echo e(__('Chapter Title')); ?></h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="<?php echo e(route('instructor.course-chapter.store', request('id'))); ?>" class="instructor__profile-form" method="post">
          <?php echo csrf_field(); ?>
          <div class="col-md-12">
            <div class="form-grp">
                <label for="title"><?php echo e(__('Title')); ?> <code>*</code></label>
                <input id="title" name="title" type="text" value="">
            </div>
          </div>
          <div class="text-end">
            <button type="submit" class="btn btn-primary"><?php echo e(__('Create')); ?></button>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>
<?php /**PATH /var/www/html/pro/resources/views/frontend/instructor-dashboard/course/partials/add-new-section-modal.blade.php ENDPATH**/ ?>