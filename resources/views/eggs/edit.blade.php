@extends('layouts.app')

   @section('content')
   <div class="container mx-auto px-4 py-8 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
       <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Edit Egg Record</h2>
       <form method="POST" action="{{ route('eggs.update', $egg) }}"
           class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md">
           @csrf
           @method('PUT')
           <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
               <div>
                   <label class="block text-gray-700 dark:text-gray-300">Pen / Flock</label>
                   <select name="pen_id" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                       <option value="">Select Pen</option>
                       @foreach ($pens as $pen)
                           <option value="{{ $pen->id }}" {{ old('pen_id', $egg->pen_id) == $pen->id ? 'selected' : '' }}>{{ $pen->name }}</option>
                       @endforeach
                   </select>
                   @error('pen_id')
                       <p class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</p>
                   @enderror
               </div>
               <div>
                   <label class="block text-gray-700 dark:text-gray-300">Date Laid</label>
                   <input type="date" name="date_laid" value="{{ old('date_laid', $egg->date_laid->format('Y-m-d')) }}"
                       class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                   @error('date_laid')
                       <p class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</p>
                   @enderror
               </div>
               <div>
                   <label class="block text-gray-700 dark:text-gray-300">Small Eggs</label>
                   <input type="number" name="small_eggs" value="{{ old('small_eggs', $egg->small_eggs) }}"
                       class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                   @error('small_eggs')
                       <p class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</p>
                   @enderror
               </div>
               <div>
                   <label class="block text-gray-700 dark:text-gray-300">Medium Eggs</label>
                   <input type="number" name="medium_eggs" value="{{ old('medium_eggs', $egg->medium_eggs) }}"
                       class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                   @error('medium_eggs')
                       <p class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</p>
                   @enderror
               </div>
               <div>
                   <label class="block text-gray-700 dark:text-gray-300">Large Eggs</label>
                   <input type="number" name="large_eggs" value="{{ old('large_eggs', $egg->large_eggs) }}"
                       class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                   @error('large_eggs')
                       <p class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</p>
                   @enderror
               </div>
               <div>
                   <label class="block text-gray-700 dark:text-gray-300">Cracked Eggs</label>
                   <input type="number" name="cracked_eggs" value="{{ old('cracked_eggs', $egg->cracked_eggs) }}"
                       class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                   @error('cracked_eggs')
                       <p class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</p>
                   @enderror
               </div>
               <div>
                   <label class="block text-gray-700 dark:text-gray-300">Collected By</label>
                   <select name="collected_by" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                       <option value="">Select Staff</option>
                       @foreach ($users as $user)
                           <option value="{{ $user->id }}" {{ old('collected_by', $egg->collected_by) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                       @endforeach
                   </select>
                   @error('collected_by')
                       <p class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</p>
                   @enderror
               </div>
           </div>
           <div class="mt-6">
               <button type="submit"
                   class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                   Update
               </button>
               <a href="{{ route('eggs.index') }}"
                   class="ml-4 text-gray-600 dark:text-gray-300 hover:underline">Cancel</a>
           </div>
       </form>
   </div>
   @endsection
   