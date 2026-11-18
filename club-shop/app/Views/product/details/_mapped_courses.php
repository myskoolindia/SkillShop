<?php
/**
 * Mapped Courses tab content
 * Variables:
 *   $mappedCourses - Array of mapped course objects/arrays
 *   $product       - Current product object
 */
?>
<div class="product-mapped-courses-container" style="padding: 10px 0;">
    <div style="margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; border-bottom: 1px solid #f1f5f9; padding-bottom: 14px;">
        <div>
            <h4 style="font-size: 17px; font-weight: 700; color: #1e293b; margin: 0 0 4px;">
                <i class="fa fa-graduation-cap text-primary" style="margin-right: 6px;"></i> Included &amp; Recommended Courses
            </h4>
            <p style="font-size: 13px; color: #64748b; margin: 0;">
                Curriculum courses mapped with this package to enhance hands-on learning and skill training.
            </p>
        </div>
        <div>
            <span class="badge badge-primary" style="font-size: 12px; padding: 6px 12px; border-radius: 20px; font-weight: 600;">
                <?= count($mappedCourses); ?> <?= count($mappedCourses) === 1 ? 'Course' : 'Courses'; ?> Available
            </span>
        </div>
    </div>

    <div class="row">
        <?php foreach ($mappedCourses as $c): 
            $courseId = $c['course_id'] ?? 0;
            $courseTitle = $c['course_title'] ?? 'Course';
            $courseCode = $c['course_code'] ?? '';
            $courseTypeName = $c['course_type_name'] ?? 'Curriculum';
            $slug = !empty($c['slug']) ? $c['slug'] : $courseId;
            $courseUrl = mainAppUrl('course-detail/' . $slug);
            $thumbnail = !empty($c['thumbnail']) ? mainAppUrl($c['thumbnail']) : '';
            $duration = !empty($c['duration']) ? $c['duration'] : '';
            $desc = !empty($c['description']) ? strip_tags($c['description']) : '';
            $isComponentMatch = !empty($c['is_component_match']);
            $mappedProdTitle = !empty($c['mapped_product']['product_title']) ? $c['mapped_product']['product_title'] : '';
        ?>
            <div class="col-12 col-md-6 m-b-20">
                <div class="card h-100" style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.04); transition: transform 0.2s, box-shadow 0.2s; display: flex; flex-direction: column;">
                    <?php if (!empty($thumbnail)): ?>
                        <div style="height: 160px; overflow: hidden; background: #f8fafc; position: relative;">
                            <img src="<?= esc($thumbnail); ?>" alt="<?= esc($courseTitle); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            <span style="position: absolute; top: 12px; left: 12px; background: rgba(15, 23, 42, 0.75); color: #ffffff; padding: 3px 10px; border-radius: 14px; font-size: 11px; font-weight: 600; backdrop-filter: blur(4px);">
                                <?= esc($courseTypeName); ?>
                            </span>
                        </div>
                    <?php else: ?>
                        <div style="height: 90px; background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); display: flex; align-items: center; justify-content: space-between; padding: 0 20px; position: relative;">
                            <i class="fa fa-graduation-cap" style="font-size: 38px; color: rgba(255,255,255,0.4);"></i>
                            <span style="background: rgba(255, 255, 255, 0.2); color: #ffffff; padding: 3px 10px; border-radius: 14px; font-size: 11px; font-weight: 600;">
                                <?= esc($courseTypeName); ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <div style="padding: 16px 18px; flex: 1 1 auto; display: flex; flex-direction: column;">
                        <?php if ($isComponentMatch && !empty($mappedProdTitle)): ?>
                            <div style="margin-bottom: 8px;">
                                <span class="badge" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; font-size: 11px; font-weight: 600; padding: 4px 8px; border-radius: 4px; white-space: normal; text-align: left;">
                                    <i class="fa fa-cube text-primary"></i> Linked to: <?= esc($mappedProdTitle); ?>
                                </span>
                            </div>
                        <?php endif; ?>

                        <h5 style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0 0 6px; line-height: 1.4;">
                            <a href="<?= esc($courseUrl); ?>" target="_blank" style="color: inherit; text-decoration: none;">
                                <?= esc($courseTitle); ?>
                            </a>
                        </h5>

                        <?php if (!empty($courseCode)): ?>
                            <div style="font-size: 11px; color: #64748b; margin-bottom: 10px;">
                                <span style="font-family: monospace; background: #f8fafc; border: 1px solid #e2e8f0; padding: 2px 6px; border-radius: 4px;">
                                    Code: <?= esc($courseCode); ?>
                                </span>
                                <?php if (!empty($duration)): ?>
                                    <span style="margin-left: 8px; color: #64748b;">
                                        <i class="fa fa-clock-o"></i> <?= esc($duration); ?> <?= is_numeric($duration) ? 'Hours' : ''; ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($desc)): ?>
                            <p style="font-size: 13px; color: #475569; line-height: 1.5; margin: 0 0 16px; flex-grow: 1; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                <?= esc(characterLimiter($desc, 140)); ?>
                            </p>
                        <?php else: ?>
                            <div style="flex-grow: 1;"></div>
                        <?php endif; ?>

                        <div style="margin-top: auto; padding-top: 12px; border-top: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between;">
                            <span style="font-size: 12px; color: #16a34a; font-weight: 600;">
                                <i class="fa fa-check-circle"></i> Integrated Curriculum
                            </span>
                            <a href="<?= esc($courseUrl); ?>" target="_blank" class="btn btn-sm btn-outline-primary" style="font-weight: 600; border-radius: 6px; padding: 5px 14px; font-size: 12px;">
                                View Course <i class="fa fa-external-link" style="margin-left: 4px; font-size: 11px;"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
