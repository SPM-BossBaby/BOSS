# Login
## Timeline

| Stage        | Status                                                 | Planned Duration  | Changes |
| ------------ | ------------------------------------------------------ | ----------------- | ------- |
| PRD          | ![Done](https://img.shields.io/badge/-Done-grey)   | 1 Sept- 6 Sept    |         |
| Development | ![Waiting](https://img.shields.io/badge/-Waiting-grey) | 9 Sept - 10 Sept  |         |
| QA           | ![Waiting](https://img.shields.io/badge/-Waiting-grey) | 11 Sept - 11 Sept |         |
| UAT          | ![Waiting](https://img.shields.io/badge/-Waiting-grey) | 12 Sept - 12 Sept |         |
| Release      | ![Waiting](https://img.shields.io/badge/-Waiting-grey) | 13 Sept - 13 Sept |         |

## Tickets
### Task
| Name | Status | Start Date | End Date | Developer 1 | Developer 2 | QA |
| - | - | - | - | - | - | - |
| [Login] Create Test Plan| ![Waiting](https://img.shields.io/badge/-Waiting-grey) | 1 Sept | 3 Sept |  |  |  
| [Login] Create Test Cases| ![Waiting](https://img.shields.io/badge/-Waiting-grey) | 3 Sept | 5 Sept |  |  | 
| [Login] Design Login Mockups| ![Done](https://img.shields.io/badge/-Done-grey) | 3 Sept | 5 Sept |  |  | 
| [Login] Logo | ![Waiting](https://img.shields.io/badge/-Waiting-grey) | 9 Sept | 9 Sept | Xin Pin | Nicolas |  |
| [Login] Username and Password fields | ![Waiting](https://img.shields.io/badge/-Waiting-grey) | 9 Sept | 9 Sept | Trevor | Xuan Lin |  |
| [Login] Remember Me checkbox | ![Waiting](https://img.shields.io/badge/-Waiting-grey) | 9 Sept | 9 Sept | Trevor | Xuan Lin |  |
| [Login] Error message | ![Waiting](https://img.shields.io/badge/-Waiting-grey) | 9 Sept | 9 Sept | Trevor | Xuan Lin |  |
| [Login] Submit button | ![Waiting](https://img.shields.io/badge/-Waiting-grey) | 9 Sept | 9 Sept | Trevor | Xuan Lin |  |
| [Login] Connect to sample data | ![Waiting](https://img.shields.io/badge/-Waiting-grey) | 10 Sept | 10 Sept | Trevor | Xuan Lin |  |
| [Login] Authentication API | ![Waiting](https://img.shields.io/badge/-Waiting-grey) | 11 Sept | 11 Sept | Xin Pin | Nicolas |  |
| [Login] Implement Authentication API | ![Waiting](https://img.shields.io/badge/-Waiting-grey) | 12 Sept | 12 Sept | Jason | Nicolas |  |
| [Login] Implement Bootstrap | ![Waiting](https://img.shields.io/badge/-Waiting-grey) | 14 Sept | 15 Sept | Jason | Nicolas |  |
| [Login] Implement Laravel | ![Waiting](https://img.shields.io/badge/-Waiting-grey) | 15 Sept | 15 Sept | Jason | Nicolas |  |
| [Login] Run Test cases | ![Waiting](https://img.shields.io/badge/-Waiting-grey) | 15 Sept | 15 Sept |  |  |  |
| [Login] Resolve bugs | ![Waiting](https://img.shields.io/badge/-Waiting-grey) | 15 Sept | 15 Sept | Jason | Nicolas |  |


### Bug
| Name | Status | Start Date | End Date | Developer 1 | Developer 2 | QA |
| - | - | - | - | - | - | - |
|  |  |  |  |  |  |  |

## Background
- Login
  - Student
    - A student will log in with his/her email ID and password.
    - Upon success, the student should be able to see the balance e$ along with a welcome message.
    - Upon failure, the app outputs a proper error message and requests the user to login again.
  - Admin
    - An administrator will log in with the username "admin" and password
    - The admin will login from the same login page as student users.
    - (inference) An admin will log in into a dashboard
    - Failure will follow the same case as Student

## Data Flow

## Mockup
Login Page:<br>
<img src="mockups\login.png" width="500">
<img src="mockups\login_fail.png" width="500">
<img src="mockups\login.gif" width="500">
<br>Landing Page:   
<br><img src="mockups\landing.png" width="500">   

## Icons
For login page: 
<br><img src="icons\bios.png" width="100"><img src="icons\mu.png" width="100">   
<br>For all other pages:   
<br><img src="icons\MUbios.png" width="100">   

## Flow
1. User enters userid and password into login page.
2. User checks "Remember me" checkbox.
3. User clicks "Login" button.
   1. The userid and password is authenticated through JSON web service.
   2. If unsuccessful, show "Please enter a valid username and password.".
   3. If successful, store userid in session variable and direct to landing page.


## Assignments
- Project Manager:
- Pair programming:
  | Driver | Observer |
  | - | - |
  