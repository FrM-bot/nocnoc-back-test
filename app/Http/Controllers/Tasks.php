<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage; 
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Enums\Status;
use App\Models\File;
use App\Models\Task;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Enums\UserType;

class Tasks extends Controller
{
    //
    public function createTask (Request $request) {
        // store files in local
        // Storage::disk('local')->put('example.txt', 'Contents');
        try {
            //code...
            // ['title' => $title, 'description' => $description] = $request;
            $payload = $request->session()->get('payload');
            $role = $payload->role;

            if ($role !== UserType::ADMIN) {
                return response([
                    'status' => Status::ERROR,
                    'error' => 'Not allowed.'
                ], 403);
            }

            $title = $request->input('title');
            $description = $request->input('description');
            $files = $request->file('files');

            $urls = [];

            // generate unique folder name
            $folderName = Str::random(20);

            // $table->timestamp('created_at')->useCurrent();
            // $table->timestamp('updated_at')->useCurrentOnUpdate();
            $newTask = Task::create([
                'title' => $title,
                'description' => $description,
                'folder_name' => $folderName
            ]);

            foreach ($files as $file) {
                $fileName = Str::random(20) . '.' . $file->getClientOriginalExtension();

                $uploadedFile = Storage::disk('public')->putFileAs($folderName, $file, $fileName);
    
                $urlPathFile = Storage::url($uploadedFile);
    
                $url = url($urlPathFile);

                File::create([
                    'name' => $fileName,
                    'original_name' => $file->getClientOriginalName(),
                    'directory' => $uploadedFile,
                    'folder_name' => $folderName,
                    'url' => $url,
                    'task_id' => $newTask->id
                ]);

                array_push($urls, $url);
            }
            // delete upload folder
            // Storage::deleteDirectory('public\uploads');
            
            // Storage::disk('local')->put($file->getClientOriginalName(), $file);
            // $file = Storage::disk('local')->get('WhatsApp Image 2024-03-28 at 14.18.38_e4de6b7c.jpg');

            return response([
                'status' => Status::SUCCESS,
                'data' => [
                    'task' => $newTask,
                    'files' => $urls,
                ]
            ], 200);
        } catch (Exception $error) {
            return response([
                'status' => Status::ERROR,
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function getTasks () {
        try {
            $tasks = Task::all(['tasks.id', 'tasks.title', 'tasks.description', 'tasks.folder_name'])->map(function (object $task) {
                $files = File::where('files.folder_name', '=', $task->folder_name)->get(['files.url', 'files.original_name as originalName']);
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'description' => $task->description,
                    'folderName' => $task->folder_name,
                    'files' => $files,
                ];
            });

            return response([
                'status' => Status::SUCCESS,
                'data' => $tasks
            ], 200);
        } catch (Exception $error) {
            return response([
                'status' => Status::ERROR,
                'error' => $error->getMessage()
            ], 500);
        }
    }
}
