<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LoginHistory>
 */
class DummyLoginHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $names = ['Aarav', 'Aryan', 'Arnav', 'Abhay', 'Aditya', 'Akash', 'Amit', 'Ankit', 'Alok', 'Anand', 'Arvind', 'Ashok', 'Atul', 'Bharat', 'Brijesh', 'Chirag', 'Dhruv', 'Divyesh', 'Gaurav', 'Gopal', 'Himanshu', 'Ishan', 'Jayesh', 'Jatin', 'Kunal', 'Manish', 'Mayank', 'Mukesh', 'Nikhil', 'Nitin', 'Parth', 'Piyush', 'Pranav', 'Prashant', 'Rajat', 'Rahul', 'Rakesh', 'Rishabh', 'Ritesh', 'Rohit', 'Sachin', 'Sandeep', 'Sanjay', 'Saurabh', 'Shubham', 'Siddharth', 'Sumit', 'Sunil', 'Suresh', 'Tarun', 'Umesh', 'Varun', 'Vikas', 'Vikram',"Nitin Sharma","Parth Shah","Pranav Desai","Prashant Gupta","Prateek Patel","Praveen Kumar","Preeti Sharma","Rishabh Jain","Ritesh Kumar","Ronak Shah","Rohit Mehta","Sachin Sharma"];
        $randomSerialNumber = DB::table('q_r_code_items')->inRandomOrder()->first();
        $dateTime = $this->faker->dateTimeBetween('2023-04-12', '2023-04-14');

        return [
            'name' => $this->faker->unique()->randomElement($names),
            'q_r_code_scanned' => $randomSerialNumber->serial_number,
            'ip_address' => $this->faker->ipv4,
            'created_at' => $dateTime->format('Y-m-d H:i:s'),
            'updated_at' => $dateTime->format('Y-m-d H:i:s'),
        ];
    }
}
