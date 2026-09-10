<?php
require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/classes/form/offering_form.php');
require_once(__DIR__ . '/classes/service/offering_service.php');
$context = context_system::instance(); require_login(); require_capability('local/trainingportal:manageofferings', $context);
$id = optional_param('id', 0, PARAM_INT);
$PAGE->set_context($context); $PAGE->set_url(new moodle_url('/local/trainingportal/offerings.php', ['id' => $id])); $PAGE->set_title(get_string('offerings', 'local_trainingportal')); $PAGE->set_heading(get_string('offerings', 'local_trainingportal'));
echo $OUTPUT->header();
if ($id) { $form = new \local_trainingportal\form\offering_form(null, null); $form->set_data(\local_trainingportal\service\offering_service::get($id)); } else { $form = new \local_trainingportal\form\offering_form(); }
if ($form->is_cancelled()) { redirect(new moodle_url('/local/trainingportal/offerings.php')); }
if ($data = $form->get_data()) { require_sesskey(); \local_trainingportal\service\offering_service::save($data, $id ?: null); redirect(new moodle_url('/local/trainingportal/offerings.php'), get_string('changessaved')); }
echo $OUTPUT->single_button(new moodle_url('/local/trainingportal/offerings.php', ['edit' => 1]), get_string('createoffering', 'local_trainingportal'));
$table = new html_table(); $table->head = [get_string('code', 'local_trainingportal'), get_string('title', 'local_trainingportal'), get_string('active', 'local_trainingportal'), get_string('actions')];
foreach (\local_trainingportal\service\offering_service::all() as $offering) { $table->data[] = [s($offering->code), s($offering->title), $offering->active ? get_string('yes') : get_string('no'), html_writer::link(new moodle_url('/local/trainingportal/offerings.php', ['id' => $offering->id]), get_string('edit'))]; }
echo html_writer::table($table);
if (optional_param('edit', 0, PARAM_BOOL) || $id) { echo $OUTPUT->heading($id ? get_string('editoffering', 'local_trainingportal') : get_string('createoffering', 'local_trainingportal'), 3); $form->display(); }
echo $OUTPUT->footer();
