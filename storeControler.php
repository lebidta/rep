public function store(Request $request)
{
    $request->validate([
        'title' => 'required|max:255',
        'content' => 'required',
        'make_id' => 'required'
    ]);

    if (auth()->guest() || !auth()->user()->hasRole('moderator') || !auth()->user()->canAddContent()) {
        return redirect('/cars/models')->with('error', 'У вас нет прав для добавления новой модели');
    }

    $make = Make::find($request->make_id);

    if (!$make) {
        return redirect('/cars/models')->with('error', 'Неверная марка');
    }

    $model = $make->model()->create($request->all());

    if ($request->hasFile('image') && config('app.uploading_enabled')) {
        Image::make($request->file('image'))
             ->resize(300, null, function ($constraint) {
                 $constraint->aspectRatio();
             })
             ->save($public_path('images/models') . DIRECTORY_SEPARATOR . $model->id . '.jpg');
        }
    }

    return redirect('/cars/models')->with('message', 'Модель успешно добавлена');
}
