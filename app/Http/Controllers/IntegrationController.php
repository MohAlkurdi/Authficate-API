<?php

namespace App\Http\Controllers;

use App\Models\UserCertificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class IntegrationController extends Controller
{
    public function getCertificates(Request $request)
    {
        if (Auth::check()) {

            try {
                $response = Http::post('http://127.0.0.1:8000/api/login', [
                    'email' => $request->email,
                    'password' => $request->password,
                ]);

                if ($response->successful()) {
                    $token = $response->json()['token'];

                    // Call the getUserCourses function with the obtained token
                    $coursesResponse = $this->getUserCourses($token);

                    if (isset($coursesResponse['certificate']) && is_array($coursesResponse['certificate'])) {
                        // Process and store the certificates in the user_certificates table
                        foreach ($coursesResponse['certificate'] as $certificate) {
                            // Check if a record already exists for the same user and course
                            $existingRecord = UserCertificate::where('user_id', Auth::id())
                                ->where('course_name', $certificate['course']['name'])
                                ->first();

                            if (! $existingRecord) {
                                UserCertificate::create([
                                    'user_id' => Auth::id(),
                                    'username' => Auth::user()->name,
                                    'course_name' => $certificate['course']['name'],
                                ]);
                            }
                        }
                    }

                    return $coursesResponse;
                } else {
                    return [
                        'error' => 'Error in obtaining the token from Certificates API.',
                        'status' => $response->status(),
                        'response' => $response->json(),
                    ];
                }
            } catch (\Exception $e) {
                return [
                    'error' => 'An error occurred while making the request to Certificates API.',
                    'message' => $e->getMessage(),
                ];
            }
        } else {
            return [
                'error' => 'User is not authenticated.',
            ];
        }
    }

    public function getUserCourses($certificatesToken)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$certificatesToken,
            ])->get('easelearnapi.mohalkurdi.com/api/user/courses');

            if ($response->successful()) {
                return $response->json();
            } else {
                return [
                    'error' => 'Error in getting user courses from Certificates API.',
                    'status' => $response->status(),
                    'response' => $response->json(),
                ];
            }
        } catch (\Exception $e) {
            return [
                'error' => 'An error occurred while making the request to Certificates API for user courses.',
                'message' => $e->getMessage(),
            ];
        }
    }

    // Search for a user certificate by certificate unique id

    public function searchCertificate(string $certificateId)
    {
        try {
            $response = Http::get('easelearnapi.mohalkurdi.com/api/certificate/'.$certificateId);

            if ($response->successful()) {
                return $response->json();
            } else {
                return [
                    'error' => 'Error in getting user courses from Certificates API.',
                    'status' => $response->status(),
                    'response' => $response->json(),
                ];
            }
        } catch (\Exception $e) {
            return [
                'error' => 'An error occurred while making the request to Certificates API for user courses.',
                'message' => $e->getMessage(),
            ];
        }
    }

    public function getUserCertificatesByMemberIdd($memberId)
    {
        // Make an HTTP request to the first API to fetch user certificates
        $firstApiResponse = Http::get('easelearnapi.mohalkurdi.com/api/user/certificates/'.$memberId);

        // Make an HTTP request to the second API to fetch user certificates
        $secondApiResponse = Http::get('learnhubapi.mohalkurdi.com/api/user/certificates/'.$memberId);

        if ($firstApiResponse->successful() && $secondApiResponse->successful()) {
            // Process the responses and merge the certificates
            $firstApiCertificates = $firstApiResponse->json();
            $secondApiCertificates = $secondApiResponse->json();

            // Merge the certificates from both APIs
            $mergedCertificates = array_merge_recursive($firstApiCertificates, $secondApiCertificates);

            return response()->json(['certificates' => $mergedCertificates], 200);
        } else {
            return response()->json(['error' => 'Unable to fetch user certificates'], 500);
        }
    }

    public function getUserCertificatesByMemberId($memberId)
    {
        // Initialize variables to store certificates
        $firstApiCertificates = [];
        $secondApiCertificates = [];

        // Make an HTTP request to the first API to fetch user certificates
        $firstApiResponse = Http::get('easelearnapi.mohalkurdi.com/api/user/certificates/'.$memberId);

        // Make an HTTP request to the second API to fetch user certificates
        $secondApiResponse = Http::get('learnhubapi.mohalkurdi.com/api/user/certificates/'.$memberId);

        if ($firstApiResponse->successful()) {
            $firstApiCertificates = $firstApiResponse->json();
        }

        if ($secondApiResponse->successful()) {
            $secondApiCertificates = $secondApiResponse->json();
        }

        // Check if there are certificates in both responses
        if (! empty($firstApiCertificates) && ! empty($secondApiCertificates)) {
            // Merge the certificates from both APIs
            $mergedCertificates = array_merge_recursive($firstApiCertificates, $secondApiCertificates);

            return response()->json(['certificates' => $mergedCertificates], 200);
        } elseif (! empty($firstApiCertificates)) {
            return response()->json(['certificates' => $firstApiCertificates], 200);
        } elseif (! empty($secondApiCertificates)) {
            return response()->json(['certificates' => $secondApiCertificates], 200);
        } else {
            return response()->json(['message' => 'No certificates found'], 200);
        }
    }
}
