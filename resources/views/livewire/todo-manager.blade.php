<?php

use Livewire\Volt\Component;

new class extends Component {
    public Todo $todo;
    public string $todoName = '';
    public ?int $editingTodoId = null;
    public string $editingTodoName = '';

    // creating todos
    public function createTodo(){
        $this->validate(['todoName' => 'required|min:3']);

        Auth::user()->todos()->create([
            'name'=> $this->pull('todoName'),
        ]);
    }


    // Editing todos
    public function editTodo(int $id){
        $todo = Auth::user()->todos()->find($id);

        if($todo){
            $this->editingTodoId = $id;
            $this->editingTodoName = $todo->name;
        }
    }


    // updating todos
    public function updateTodo(){

        $this->validate([
            'editingTodoName' => 'required|min:3',
        ]);

        $todo = Auth::user()->todos()->find($this->editingTodoId);

        $this->authorize('update', $todo);
        $todo->update([
            'name' => $this->editingTodoName,
        ]);

        $this->editingTodoId = null;
        $this->editingTodoName = '';
    }


    // Deleting todos
    public function deleteTodo(int $id){
        $todo = Auth::user()->todos()->find($id);
        $this->authorize('delete', $todo);
        $todo->delete();
    }


    

    // display todos on page load
    public function with(){
        return [
            'todos' => Auth::user()->todos()->get(),
        ];
    }
}; ?>

<div class="flex flex-col space-y-5 max-w-lg mx-auto mt-10 p-4 bg-white rounded shadow-md">
    {{-- create todo form --}}
    <form wire:submit.prevent='createTodo' class="my-5">
        <div class="flex space-x-2">
            <x-text-input wire:model.defer="todoName" class="flex-1 px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring focus:border-blue-300" placeholder="Enter new todo..." />
            <x-primary-button type="submit" class="px-4 py-2 bg-blue-500 text-white font-semibold rounded hover:bg-blue-600" style="margin-left: 2px">Add Todo</x-primary-button>
        </div>
        <x-input-error :messages="$errors->get('todoName')" class="mt-2 text-red-500"/>
    </form>

    {{-- todo list --}}
    <div> <!-- Using space-y-* for vertical spacing -->
        @foreach ($todos as $todo)
            <div wire:key='{{ $todo->id }}' style="margin: 10px 0px;" class="flex items-center justify-between bg-gray-100 py-2 px-2 rounded shadow-sm">
                {{-- show either the name or the edit input depending the state --}}
                @if ($editingTodoId === $todo->id)
                    <x-text-input wire:model.defer="editingTodoName" type="text" class="flex-1 px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring focus:border-blue-300"/>
                    <x-secondary-button wire:click="updateTodo" style="color:white; background-color:green">Save</x-secondary-button>
                    <x-secondary-button wire:click="$set('editingTodoId', null)" style="color:white; background-color:red">Cancel</x-secondary-button>
                @else
                    <span>{{$todo->name}}</span>
                    <div>
                        <x-secondary-button wire:click="editTodo({{$todo->id}})" style="color:white; background-color:green">edit</x-secondary-button>
                        <x-secondary-button wire:click="deleteTodo({{$todo->id}})" style="color:white; background-color:red">Delete</x-secondary-button>
                    </div>
                @endif
                
            </div>
        @endforeach
    </div>
</div>

