<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Input;
Use Alert;
use Image;
use App\Courses;
use App\Category;

class CourseController extends Controller
{
     public function addCourse(Request $request){
    	if($request->isMethod('post')){
    		$data=$request->all();
    		// print_r($data);die;
    		$course=new Courses;
            $course->category_id=$data['category_id'];
    		$course->title=$data['course_title'];
    		$course->code=$data['course_code'];
    		$course->author=$data['course_author'];
    		if (!empty($data['course_description'])) {
    			$course->description=$data['course_description'];
    		}else{
    			$course->description='';
    		}
    		$course->price=$data['course_price'];
    		//Upload image
    	   	if($request->hasfile('image')){
    			echo $img_tmp= $request->image;
    			if($img_tmp->isValid()){
    			//image path code
    			$extension= $img_tmp->getClientOriginalExtension();
    			$filename=rand(111,99999).'.'.$extension;
    			$img_path='uploads/courses/'.$filename;
    			//image resize
    			Image::make($img_tmp)->resize(500,500)->save($img_path);
    			$course->image=$filename;
    		}
    		}
    		$course->save();
    		return redirect('/admin/view-course')->with('flash_message_success','Course has been added successfully');
    	}
        // Categories Dropdown  menu code
        $categories=Category::where(['parent_id'=>0])->get();
        $categories_dropdown="<option value='' selected disabled>Select</option>";
        foreach ($categories as $cat) {
            $categories_dropdown.="<option value='".$cat->id."'>".$cat->name."</option>";
            $sub_categories= Category::where(['parent_id'=>$cat->id])->get();
            foreach ($sub_categories as $sub_cat) {
                 $categories_dropdown.="<option value='".$sub_cat->id."'>&nbsp;--&nbsp".$sub_cat->name."</option>";

            }
        }
    	return view('admin.courses.add_course');
    }

    public function viewCourses(){
    	$courses=Courses::get();
    	return view('admin.courses.view_course')->with(compact('courses'));
    }

    public function editCourse(Request $request,$id=null){
    	if($request->isMethod('post')){
    		$data=$request->all();
    		//Upload image
    	   	if($request->hasfile('image')){
    			echo $img_tmp= $request->image;
    			if($img_tmp->isValid()){
    			//image path code
    			$extension= $img_tmp->getClientOriginalExtension();
    			$filename=rand(111,99999).'.'.$extension;
    			$img_path='uploads/courses/'.$filename;
    			//image resize
    			Image::make($img_tmp)->resize(500,500)->save($img_path);
    		}
    		}else{
    			return redirect()->back()->with('flash_message_error','Please select image!!');

    		}
    		if(empty($data['course_description'])) {
    			$data['course_description']='';
    		}
    		Courses::where(['id'=>$id])->update(['title'=>$data['course_title'],'code'=>$data['course_code'],'author'=>$data['course_author'],'description'=>$data['course_description'],'price'=>$data['course_price'],'image'=>$filename]);
    		return redirect('/admin/view-course')->with('flash_message_success','Course has been updated!!');
    	}
    	$courseDetails=Courses::where(['id'=>$id])->first();
        // Category dropdown code
        $categories=Category::where(['parent_id'=>0])->get();
        $categories_dropdown= "<option value='' selected disabled>Select</option>";
        foreach($categories as $cat){
            if($cat->id==$courseDetails->category_id){
                $selected="selected";
            }else{
                $selected="";  
            }
            $categories_dropdown.="<option value='".$cat->id."' ".$selected.">".$cat->name."</option>";
        }
        //Code for sub-categories
        $sub_categories=Category::where(['parent_id'=>$cat->id])->get();
        foreach($sub_categories as $sub_cat){
            if($sub_cat->id==$courseDetails->category_id){
                $selected="selected";
            }else{
                $selected="";  
            }
             $categories_dropdown.="<option value='".$sub_cat->id."' ".$selected.">&nbsp;--&nbsp".$sub_cat->name."</option>";
        }


    	return view('admin.courses.edit_course')->with(compact('courseDetails'));
    }

    public function DeleteCourse($id){
    	Courses::where(['id'=>$id])->delete();
    	Alert::success('Deleted Successfully', 'Success Message');
    	return redirect()->back()->with('flash_message_error','Course Deleted');
    }

    public function updateStatus(Request $request,$id=null){
        $data=$request->all();
        Courses::where('id',$data['id'])->update(['status'=>$data['status']]);
    }


}
