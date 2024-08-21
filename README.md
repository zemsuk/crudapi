# crudapi
Crud Api for Headless cms

#How to use
    $data['model'] = "Course";
    $data['fields'] = ['id', 'lenght', 'university_id', 'study_subject_id', 'status'];
    $data['fields'] = ['courses.*', 'study_subjects.name as subject_name', 'study_levels.name as level_name', 'universities.name as university_name', 'universities.cost_of_living'];
    $data['join']['universities'] = ['universities.id', 'courses.university_id'];
    $data['join']['study_subjects'] = ['study_subjects.id', 'courses.study_subject_id'];
    $data['join']['study_levels'] = ['study_levels.id', 'courses.study_level_id'];
    $data['where'] = [['courses.status', '=', 1], ['study_levels.status', '=', 1]];
    $data['order_by'] = ['courses.id', 'DESC'];
    $data['group_by'] = 'courses.university_id';
    $data['pagination'] = '4';
    $data['limit'] = '10';
    $data['view'] = 'courses/courses';
    return $this->restapi($data); 

#Field Type
const zems_fields = [
    {
      pk:"id",
      basic:"",
      basic:"Custom Label",
      basic:"|required",
      basic:"Custom Label|required",
      name:{type:'input',name:"name", label:"Name"},
      phone:"Patient Phone", 
      email:"", 
      address:"address", 
      dob:{field:"input", taype:"date"}, 
      location_id:"location_id", 
      sex:{type:"radio",option:[{label:"Male", value:1},{label:"Female", value:2}],name:"sex", label:"Gender"}, 
      kin_name:"kin_name", 
      kin_phone:"kin_phone"
    },
    // 2nd column
    {
      avatar:{name:'avatar'}
    }
  ]
